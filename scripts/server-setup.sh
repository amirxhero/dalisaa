#!/usr/bin/env bash
#
# Salika (Laravel 12) - Complete production server setup
# Ubuntu 22.04 / 24.04 LTS
#
# Usage (as root on a fresh VPS):
#   chmod +x scripts/server-setup.sh
#   sudo ./scripts/server-setup.sh
#
# Before running:
#   1. Point dalisaa.ir and www.dalisaa.ir A records to this server IP
#   2. Clone the project to APP_DIR (or let this script clone if REPO_URL is set)
#

set -euo pipefail

# ─── Configuration ────────────────────────────────────────────────────────────

DOMAIN="dalisaa.ir"
WWW_DOMAIN="www.dalisaa.ir"
APP_NAME="salika"
APP_DIR="/var/www/${APP_NAME}"
APP_USER="deploy"
APP_GROUP="www-data"

# Set REPO_URL to auto-clone, or leave empty if code is already on the server
REPO_URL=""
REPO_BRANCH="main"

# Database (change DB_PASS before production use)
DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}"
DB_PASS="${DB_PASS:-$(openssl rand -hex 16)}"

# PHP
PHP_VERSION="8.4"

# Node.js LTS (for `npm run build`)
NODE_MAJOR="22"

# Certbot email for Let's Encrypt expiry notices
CERTBOT_EMAIL="${CERTBOT_EMAIL:-admin@${DOMAIN}}"

# ─── Helpers ──────────────────────────────────────────────────────────────────

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log()  { echo -e "${GREEN}[+]${NC} $*"; }
warn() { echo -e "${YELLOW}[!]${NC} $*"; }
err()  { echo -e "${RED}[x]${NC} $*" >&2; }

require_root() {
    if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
        err "This script must be run as root (use sudo)."
        exit 1
    fi
}

detect_ubuntu() {
    if [[ ! -f /etc/os-release ]]; then
        err "Unsupported OS. This script targets Ubuntu 22.04 / 24.04."
        exit 1
    fi
    # shellcheck source=/dev/null
    source /etc/os-release
    if [[ "${ID:-}" != "ubuntu" ]]; then
        warn "Non-Ubuntu detected (${ID:-unknown}). Continuing anyway..."
    fi
}

# ─── System packages ──────────────────────────────────────────────────────────

install_base_packages() {
    log "Updating system packages..."
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -y
    apt-get upgrade -y

    log "Installing base packages..."
    apt-get install -y \
        software-properties-common \
        ca-certificates \
        curl \
        wget \
        git \
        unzip \
        zip \
        acl \
        ufw \
        supervisor \
        cron \
        nginx \
        mariadb-server \
        redis-server \
        certbot \
        python3-certbot-nginx
}

# ─── PHP 8.4 ──────────────────────────────────────────────────────────────────

install_php() {
    log "Adding ondrej/php PPA..."
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y

    log "Installing PHP ${PHP_VERSION} and extensions..."
    apt-get install -y \
        "php${PHP_VERSION}-fpm" \
        "php${PHP_VERSION}-cli" \
        "php${PHP_VERSION}-common" \
        "php${PHP_VERSION}-mysql" \
        "php${PHP_VERSION}-sqlite3" \
        "php${PHP_VERSION}-mbstring" \
        "php${PHP_VERSION}-xml" \
        "php${PHP_VERSION}-curl" \
        "php${PHP_VERSION}-zip" \
        "php${PHP_VERSION}-gd" \
        "php${PHP_VERSION}-bcmath" \
        "php${PHP_VERSION}-intl" \
        "php${PHP_VERSION}-readline" \
        "php${PHP_VERSION}-tokenizer" \
        "php${PHP_VERSION}-fileinfo" \
        "php${PHP_VERSION}-redis" \
        "php${PHP_VERSION}-imagick"

    log "Tuning PHP-FPM pool..."
    local pool="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
    sed -i 's/^;*cgi.fix_pathinfo=.*/cgi.fix_pathinfo=0/' "/etc/php/${PHP_VERSION}/fpm/php.ini" || true
    sed -i 's/^;*upload_max_filesize.*/upload_max_filesize = 64M/' "/etc/php/${PHP_VERSION}/fpm/php.ini"
    sed -i 's/^;*post_max_size.*/post_max_size = 64M/' "/etc/php/${PHP_VERSION}/fpm/php.ini"
    sed -i 's/^;*memory_limit.*/memory_limit = 256M/' "/etc/php/${PHP_VERSION}/fpm/php.ini"
    sed -i 's/^;*max_execution_time.*/max_execution_time = 120/' "/etc/php/${PHP_VERSION}/fpm/php.ini"

    # Allow larger uploads for media library
    if grep -q '^pm.max_children' "$pool"; then
        sed -i 's/^pm.max_children.*/pm.max_children = 20/' "$pool"
    fi

    systemctl enable "php${PHP_VERSION}-fpm"
    systemctl restart "php${PHP_VERSION}-fpm"
}

# ─── Composer ─────────────────────────────────────────────────────────────────

install_composer() {
    if command -v composer >/dev/null 2>&1; then
        log "Composer already installed."
        return
    fi

    log "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    chmod +x /usr/local/bin/composer
}

# ─── Node.js ──────────────────────────────────────────────────────────────────

install_node() {
    if command -v node >/dev/null 2>&1; then
        local current
        current="$(node -v | cut -d. -f1 | tr -d v)"
        if [[ "$current" -ge "$NODE_MAJOR" ]]; then
            log "Node.js $(node -v) already installed."
            return
        fi
    fi

    log "Installing Node.js ${NODE_MAJOR}.x..."
    curl -fsSL "https://deb.nodesource.com/setup_${NODE_MAJOR}.x" | bash -
    apt-get install -y nodejs
}

# ─── MariaDB ──────────────────────────────────────────────────────────────────

setup_mariadb() {
    log "Securing MariaDB and creating database..."

    systemctl enable mariadb
    systemctl start mariadb

    mysql --version

    mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
}

# ─── Deploy user & app directory ─────────────────────────────────────────────

setup_deploy_user() {
    if ! id "$APP_USER" &>/dev/null; then
        log "Creating deploy user: ${APP_USER}"
        useradd -m -s /bin/bash "$APP_USER"
        usermod -aG "$APP_GROUP" "$APP_USER"
    fi

    mkdir -p "$APP_DIR"
    chown -R "${APP_USER}:${APP_GROUP}" "$APP_DIR"
    chmod -R 775 "$APP_DIR"
}

clone_or_prepare_app() {
    if [[ -n "$REPO_URL" && ! -d "${APP_DIR}/.git" ]]; then
        log "Cloning repository into ${APP_DIR}..."
        sudo -u "$APP_USER" git clone --branch "$REPO_BRANCH" "$REPO_URL" "$APP_DIR"
    elif [[ ! -f "${APP_DIR}/artisan" ]]; then
        warn "Project not found at ${APP_DIR}/artisan"
        warn "Clone or upload the project to ${APP_DIR}, then re-run deploy_app()."
    fi
}

# ─── Laravel application setup ────────────────────────────────────────────────

deploy_app() {
    if [[ ! -f "${APP_DIR}/artisan" ]]; then
        warn "Skipping Laravel deploy — artisan not found."
        return
    fi

    log "Installing PHP dependencies..."
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && composer install --no-dev --optimize-autoloader --no-interaction"

    if [[ -f "${APP_DIR}/package.json" ]]; then
        log "Building frontend assets..."
        sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && npm ci && npm run build"
    fi

    if [[ ! -f "${APP_DIR}/.env" ]]; then
        log "Creating .env from .env.example..."
        sudo -u "$APP_USER" cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
    fi

    log "Updating .env for production..."
    local env_file="${APP_DIR}/.env"

    set_env() {
        local key="$1"
        local value="$2"
        # Escape sed replacement characters in value
        local escaped
        escaped="$(printf '%s' "$value" | sed 's/[\\/&|]/\\&/g')"
        if grep -q "^${key}=" "$env_file"; then
            sed -i "s|^${key}=.*|${key}=${escaped}|" "$env_file"
        else
            printf '%s=%s\n' "$key" "$value" >> "$env_file"
        fi
    }

    set_env "APP_NAME" "Salika"
    set_env "APP_ENV" "production"
    set_env "APP_DEBUG" "false"
    set_env "APP_URL" "https://${DOMAIN}"
    set_env "APP_TIMEZONE" "Asia/Tehran"
    set_env "APP_LOCALE" "fa"

    set_env "DB_CONNECTION" "mysql"
    set_env "DB_HOST" "127.0.0.1"
    set_env "DB_PORT" "3306"
    set_env "DB_DATABASE" "$DB_NAME"
    set_env "DB_USERNAME" "$DB_USER"
    set_env "DB_PASSWORD" "$DB_PASS"

    set_env "SESSION_DRIVER" "database"
    set_env "CACHE_STORE" "redis"
    set_env "QUEUE_CONNECTION" "redis"
    set_env "REDIS_HOST" "127.0.0.1"
    set_env "REDIS_PORT" "6379"

    set_env "LOG_CHANNEL" "daily"
    set_env "LOG_LEVEL" "warning"

    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan key:generate --force"

    log "Running migrations..."
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan migrate --force"

    log "Optimizing Laravel..."
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan storage:link"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan config:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan route:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan view:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan event:cache"

    chown -R "${APP_USER}:${APP_GROUP}" "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
}

# ─── Nginx ────────────────────────────────────────────────────────────────────

configure_nginx() {
    log "Writing Nginx site config for ${DOMAIN}..."

    local site="/etc/nginx/sites-available/${APP_NAME}"
    local php_sock="/run/php/php${PHP_VERSION}-fpm.sock"

    cat > "$site" <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} ${WWW_DOMAIN};

    root ${APP_DIR}/public;
    index index.php;

    charset utf-8;
    client_max_body_size 64M;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${php_sock};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, immutable";
    }
}
EOF

    ln -sf "$site" "/etc/nginx/sites-enabled/${APP_NAME}"
    rm -f /etc/nginx/sites-enabled/default

    nginx -t
    systemctl enable nginx
    systemctl reload nginx
}

# ─── SSL (Let's Encrypt) ──────────────────────────────────────────────────────

setup_ssl() {
    log "Requesting SSL certificate for ${DOMAIN}..."

    if certbot certificates 2>/dev/null | grep -q "${DOMAIN}"; then
        log "Certificate already exists. Renewing if needed..."
        certbot renew --quiet
    else
        certbot --nginx \
            -d "${DOMAIN}" \
            -d "${WWW_DOMAIN}" \
            --non-interactive \
            --agree-tos \
            -m "${CERTBOT_EMAIL}" \
            --redirect
    fi

    # Auto-renewal timer (certbot package installs this on Ubuntu, verify anyway)
    systemctl enable certbot.timer 2>/dev/null || true
    systemctl start certbot.timer 2>/dev/null || true
}

# ─── Supervisor (queue worker) ────────────────────────────────────────────────

configure_supervisor() {
    if [[ ! -f "${APP_DIR}/artisan" ]]; then
        warn "Skipping Supervisor — artisan not found."
        return
    fi

    log "Configuring Supervisor queue worker..."

    cat > "/etc/supervisor/conf.d/${APP_NAME}-worker.conf" <<EOF
[program:${APP_NAME}-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php ${APP_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${APP_USER}
numprocs=2
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

    systemctl enable supervisor
    systemctl restart supervisor
    supervisorctl reread
    supervisorctl update
    supervisorctl start "${APP_NAME}-worker:"* || true
}

# ─── Cron (Laravel scheduler) ─────────────────────────────────────────────────

configure_cron() {
    if [[ ! -f "${APP_DIR}/artisan" ]]; then
        warn "Skipping cron — artisan not found."
        return
    fi

    log "Adding Laravel scheduler cron job..."

    local cron_line="* * * * * ${APP_USER} cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    local cron_file="/etc/cron.d/${APP_NAME}-schedule"

    echo "$cron_line" > "$cron_file"
    chmod 644 "$cron_file"
}

# ─── Firewall ─────────────────────────────────────────────────────────────────

configure_firewall() {
    log "Configuring UFW firewall..."
    ufw --force reset
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow OpenSSH
    ufw allow 'Nginx Full'
    ufw --force enable
}

# ─── Services ─────────────────────────────────────────────────────────────────

enable_services() {
    log "Enabling and starting services..."
    systemctl enable redis-server nginx mariadb "php${PHP_VERSION}-fpm" supervisor cron
    systemctl restart redis-server nginx mariadb "php${PHP_VERSION}-fpm" supervisor cron
}

# ─── Summary ──────────────────────────────────────────────────────────────────

print_summary() {
    echo ""
    echo "════════════════════════════════════════════════════════════"
    echo -e "${GREEN}Server setup completed successfully!${NC}"
    echo "════════════════════════════════════════════════════════════"
    echo ""
    echo "  Domain:       https://${DOMAIN}"
    echo "  App path:     ${APP_DIR}"
    echo "  Deploy user:  ${APP_USER}"
    echo ""
    echo "  Database:"
    echo "    Name:       ${DB_NAME}"
    echo "    User:       ${DB_USER}"
    echo "    Password:   ${DB_PASS}"
    echo ""
    echo "  PHP:          $(php${PHP_VERSION} -v | head -1)"
    echo "  Composer:     $(composer --version 2>/dev/null || echo 'not installed')"
    echo "  Node:         $(node -v 2>/dev/null || echo 'not installed')"
    echo ""
    echo "  Next steps:"
    echo "    1. Ensure DNS for ${DOMAIN} points to this server"
    echo "    2. Set Zarinpal / mail credentials in ${APP_DIR}/.env"
    echo "    3. Run: sudo -u ${APP_USER} php ${APP_DIR}/artisan config:cache"
    echo ""
    echo "  Useful commands:"
    echo "    sudo supervisorctl status"
    echo "    sudo tail -f ${APP_DIR}/storage/logs/laravel.log"
    echo "    sudo certbot renew --dry-run"
    echo ""
    echo "  Save the database password above — it won't be shown again."
    echo "════════════════════════════════════════════════════════════"
}

# ─── Main ─────────────────────────────────────────────────────────────────────

main() {
    require_root
    detect_ubuntu

    log "Starting Salika server setup for ${DOMAIN}..."

    install_base_packages
    install_php
    install_composer
    install_node
    setup_mariadb
    setup_deploy_user
    clone_or_prepare_app
    configure_nginx
    enable_services
    deploy_app
    setup_ssl
    configure_supervisor
    configure_cron
    configure_firewall
    print_summary
}

main "$@"
