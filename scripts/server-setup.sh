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
#   2. Upload or clone the project to the server first (this script does NOT clone)
#

set -euo pipefail

# ─── Configuration ────────────────────────────────────────────────────────────

DOMAIN="dalisaa.ir"
WWW_DOMAIN="www.dalisaa.ir"
APP_NAME="dalisaa"
APP_DIR="/var/www/${APP_NAME}"
APP_USER="deploy"
APP_GROUP="www-data"

# Leave empty to auto-detect from script location (../artisan)
# APP_DIR="/var/www/dalisaa"

# Database (change DB_PASS before production use)
DB_CONNECTION="mysql"
DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}"
DB_PASS="${DB_PASS:-$(openssl rand -hex 16)}"

# PHP
PHP_VERSION="8.4"

# Node.js LTS (for `npm run build`)
NODE_MAJOR="22"

# Certbot email for Let's Encrypt expiry notices
CERTBOT_EMAIL="${CERTBOT_EMAIL:-admin@${DOMAIN}}"
CERTBOT_WEBROOT="/var/www/certbot"

# Set to false if www subdomain has DNS/redirect issues
INCLUDE_WWW="${INCLUDE_WWW:-true}"

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

is_private_ip() {
    local ip="$1"
    [[ "$ip" =~ ^10\. ]] && return 0
    [[ "$ip" =~ ^192\.168\. ]] && return 0
    [[ "$ip" =~ ^172\.(1[6-9]|2[0-9]|3[0-1])\. ]] && return 0
    return 1
}

verify_domain_for_ssl() {
    local domain="$1"
    local dns_ip redirect_target

    dns_ip="$(dig +short "$domain" A 2>/dev/null | grep -E '^[0-9]+\.' | tail -1 || true)"
    if [[ -z "$dns_ip" ]]; then
        warn "${domain}: no A record found."
        return 1
    fi
    if is_private_ip "$dns_ip"; then
        warn "${domain}: DNS points to private IP ${dns_ip}."
        return 1
    fi

    redirect_target="$(
        curl -sI --max-time 15 "http://${domain}/" 2>/dev/null \
            | tr -d '\r' \
            | awk -F': ' 'tolower($1)=="location"{print $2; exit}'
    )"
    if [[ -n "$redirect_target" ]] && echo "$redirect_target" | grep -qE 'https?://(10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)'; then
        warn "${domain}: HTTP redirects to private address → ${redirect_target}"
        return 1
    fi

    log "${domain} passed SSL preflight (DNS: ${dns_ip})."
    return 0
}

nginx_site_file() {
    local site="/etc/nginx/sites-available/${APP_NAME}"

    if [[ -f "$site" ]]; then
        echo "$site"
        return
    fi

    local found
    found="$(grep -l "server_name.*${DOMAIN}" /etc/nginx/sites-available/* 2>/dev/null | head -1 || true)"
    if [[ -n "$found" ]]; then
        echo "$found"
        return
    fi

    echo "$site"
}

nginx_enabled_name() {
    basename "$(nginx_site_file)"
}

nginx_is_configured() {
    local site
    site="$(nginx_site_file)"

    [[ -f "$site" ]] || return 1
    grep -q "server_name.*${DOMAIN}" "$site" || return 1
    grep -q "root ${APP_DIR}/public" "$site" || return 1
    grep -q "/\\.well-known/acme-challenge/" "$site" || return 1
    return 0
}

nginx_ssl_is_configured() {
    local site
    site="$(nginx_site_file)"

    [[ -f "$site" ]] || return 1
    grep -q "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" "$site" && return 0
    grep -q "ssl_certificate.*${DOMAIN}" "$site" && return 0
    return 1
}

ssl_cert_exists() {
    [[ -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" ]] \
        && [[ -f "/etc/letsencrypt/live/${DOMAIN}/privkey.pem" ]]
}

ensure_nginx_symlink() {
    local site enabled name
    site="$(nginx_site_file)"
    name="$(nginx_enabled_name)"
    enabled="/etc/nginx/sites-enabled/${name}"

    ln -sf "$site" "$enabled"
    rm -f /etc/nginx/sites-enabled/default
}

reload_nginx_if_valid() {
    nginx -t
    systemctl enable nginx
    systemctl reload nginx
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
        python3-certbot-nginx \
        dnsutils
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

setup_mysql() {
    log "Setting up MySQL (MariaDB)..."

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
    chown "${APP_USER}:${APP_GROUP}" "$APP_DIR"
    chmod 775 "$APP_DIR"
}

detect_app_dir() {
    local script_dir
    script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

    if [[ -f "${script_dir}/../artisan" ]]; then
        APP_DIR="$(cd "${script_dir}/.." && pwd)"
        APP_NAME="$(basename "$APP_DIR")"
        DB_NAME="${APP_NAME}"
        DB_USER="${APP_NAME}"
        log "Detected project at ${APP_DIR}"
    elif [[ -f "${APP_DIR}/artisan" ]]; then
        log "Using existing project at ${APP_DIR}"
    else
        err "Laravel project not found."
        err "Upload code to ${APP_DIR} or run this script from the project's scripts/ folder."
        exit 1
    fi
}

# ─── Laravel application setup ────────────────────────────────────────────────

# PHP-FPM runs as www-data; artisan runs as APP_USER. Both must write here.
fix_storage_permissions() {
    local base="${1:-${APP_DIR}}"

    mkdir -p \
        "${base}/storage/framework/cache/data" \
        "${base}/storage/framework/sessions" \
        "${base}/storage/framework/testing" \
        "${base}/storage/framework/views" \
        "${base}/storage/logs" \
        "${base}/storage/app/public" \
        "${base}/bootstrap/cache"

    chown -R "${APP_USER}:${APP_GROUP}" "${base}/storage" "${base}/bootstrap/cache"
    chmod -R ug+rwx "${base}/storage" "${base}/bootstrap/cache"
    find "${base}/storage" "${base}/bootstrap/cache" -type d -exec chmod g+s {} \;
}

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

        log "Updating .env for production..."
        local env_file="${APP_DIR}/.env"

        set_env() {
            local key="$1"
            local value="$2"
            local escaped
            escaped="$(printf '%s' "$value" | sed 's/[\\/&|]/\\&/g')"
            if grep -q "^${key}=" "$env_file"; then
                sed -i "s|^${key}=.*|${key}=${escaped}|" "$env_file"
            else
                printf '%s=%s\n' "$key" "$value" >> "$env_file"
            fi
        }

        set_env "APP_NAME" "Dalisaa"
        set_env "APP_ENV" "production"
        set_env "APP_DEBUG" "false"
        set_env "APP_URL" "https://${DOMAIN}"
        set_env "APP_TIMEZONE" "Asia/Tehran"
        set_env "APP_LOCALE" "fa"

        set_env "DB_CONNECTION" "${DB_CONNECTION}"
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
    else
        log ".env already exists — ensuring MySQL is configured..."
        local env_file="${APP_DIR}/.env"
        if grep -q '^DB_CONNECTION=sqlite' "$env_file"; then
            sed -i "s|^DB_CONNECTION=sqlite|DB_CONNECTION=${DB_CONNECTION}|" "$env_file"
        fi
    fi

    log "Running migrations..."
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan migrate --force"

    log "Optimizing Laravel..."
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan storage:link"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan config:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan route:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan view:cache"
    sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && php artisan event:cache"

    fix_storage_permissions
}

# ─── Nginx ────────────────────────────────────────────────────────────────────

configure_nginx() {
    local site php_sock
    site="$(nginx_site_file)"
    php_sock="/run/php/php${PHP_VERSION}-fpm.sock"

    mkdir -p "${CERTBOT_WEBROOT}/.well-known/acme-challenge"
    chown -R www-data:www-data "${CERTBOT_WEBROOT}"

    if nginx_ssl_is_configured; then
        log "Nginx SSL config already exists for ${DOMAIN} — skipping."
        ensure_nginx_symlink
        reload_nginx_if_valid
        return
    fi

    if nginx_is_configured; then
        log "Nginx HTTP config already exists for ${DOMAIN} — skipping."
        ensure_nginx_symlink
        reload_nginx_if_valid
        return
    fi

    log "Writing Nginx site config for ${DOMAIN}..."

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

    # Let's Encrypt HTTP-01 challenge (must NOT go through Laravel)
    location ^~ /.well-known/acme-challenge/ {
        default_type "text/plain";
        root ${CERTBOT_WEBROOT};
    }

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

    ensure_nginx_symlink
    reload_nginx_if_valid
}

# ─── SSL (Let's Encrypt) ──────────────────────────────────────────────────────

setup_ssl() {
    mkdir -p "${CERTBOT_WEBROOT}/.well-known/acme-challenge"
    chown -R www-data:www-data "${CERTBOT_WEBROOT}"

    if ssl_cert_exists && nginx_ssl_is_configured; then
        log "SSL certificate and Nginx HTTPS config already exist — skipping."
        systemctl enable certbot.timer 2>/dev/null || true
        systemctl start certbot.timer 2>/dev/null || true
        return
    fi

    if ssl_cert_exists; then
        log "SSL certificate already exists — installing into Nginx only..."
        certbot install --nginx \
            --cert-name "${DOMAIN}" \
            -d "${DOMAIN}" -d "${WWW_DOMAIN}" \
            --redirect \
            --non-interactive
        systemctl enable certbot.timer 2>/dev/null || true
        systemctl start certbot.timer 2>/dev/null || true
        return
    fi

    log "Requesting SSL certificate for ${DOMAIN}..."

    local ssl_domains=()
    if verify_domain_for_ssl "${DOMAIN}"; then
        ssl_domains+=("${DOMAIN}")
    else
        err "Primary domain ${DOMAIN} failed SSL preflight. Fix DNS first."
        return 1
    fi

    if [[ "${INCLUDE_WWW}" == "true" ]]; then
        if verify_domain_for_ssl "${WWW_DOMAIN}"; then
            ssl_domains+=("${WWW_DOMAIN}")
        else
            warn "Skipping ${WWW_DOMAIN} for now — fix its DNS/redirect, then expand the cert:"
            warn "  certbot certonly --webroot -w ${CERTBOT_WEBROOT} -d ${DOMAIN} -d ${WWW_DOMAIN} --expand"
        fi
    fi

    local cert_args=()
    for d in "${ssl_domains[@]}"; do
        cert_args+=(-d "$d")
    done

    certbot certonly --webroot \
        -w "${CERTBOT_WEBROOT}" \
        "${cert_args[@]}" \
        --non-interactive \
        --agree-tos \
        -m "${CERTBOT_EMAIL}"

    if ! nginx_ssl_is_configured; then
        certbot install --nginx \
            --cert-name "${DOMAIN}" \
            "${cert_args[@]}" \
            --redirect \
            --non-interactive
    else
        log "Nginx SSL already configured — skipping certbot install."
    fi

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

    log "Starting server setup for ${DOMAIN}..."

    detect_app_dir
    install_base_packages
    install_php
    install_composer
    install_node
    setup_mysql
    setup_deploy_user
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
