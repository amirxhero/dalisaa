import './bootstrap';

import '@majidh1/jalalidatepicker';
import '@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css';

import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, FreeMode } from 'swiper/modules';

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import TipTapImage from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import { Placeholder } from '@tiptap/extensions';

window.Swiper = Swiper;

/**
 * Generic Alpine helper for Swiper carousels. Expects three x-ref'd
 * elements inside the component root: `track` (.swiper), and optionally
 * `prev` / `next` / `pagination` for custom controls.
 *
 * <div x-data="carousel({ slidesPerView: 2 })" x-init="init()">
 *   <div class="swiper" x-ref="track">...</div>
 *   <button x-ref="prev"></button>
 *   <button x-ref="next"></button>
 * </div>
 */
Alpine.data('carousel', (options = {}) => ({
    swiper: null,
    init() {
        this.swiper = new Swiper(this.$refs.track, {
            modules: [Navigation, Pagination, Autoplay, FreeMode],
            dir: 'rtl',
            navigation: this.$refs.next && this.$refs.prev ? {
                nextEl: this.$refs.next,
                prevEl: this.$refs.prev,
            } : false,
            pagination: this.$refs.pagination ? {
                el: this.$refs.pagination,
                clickable: true,
            } : false,
            ...options,
        });
    },
}));

/** Site header: mobile drawer, cart drawer, search overlay, auth modal. */
Alpine.data('siteHeader', () => ({
    mobileMenuOpen: false,
    cartOpen: false,
    searchOpen: false,
    authOpen: false,
    activeMegaTab: 0,
}));

/**
 * Global Instagram-style story viewer. Stories (each with one or more
 * slides) are registered via setStories() from the homepage, then opened
 * by index. Auto-advances through slides/stories on a timer; "seen"
 * stories are tracked in localStorage so their ring can be dimmed.
 */
/**
 * TipTap rich-text editor Alpine component used in the admin blog post forms.
 * Exposes cmd(action) for toolbar buttons and syncs HTML to a hidden input.
 */
Alpine.data('richEditor', ({ value = '', uploadUrl = '' } = {}) => {
    // The TipTap Editor is kept in a closure variable — NOT on `this` — so
    // Alpine never wraps it in a reactive Proxy. A proxied editor breaks
    // ProseMirror's internal document-identity checks ("Applying a mismatched
    // transaction"). The reactive `tick` counter is what drives the toolbar
    // active-state bindings instead.
    let editor = null;

    return {
        uploadUrl,
        tick: 0,

        init() {
            const self = this;
            editor = new Editor({
                element: this.$refs.editorEl,
                extensions: [
                    StarterKit.configure({
                        link: { openOnClick: false, HTMLAttributes: { target: '_blank', rel: 'noopener' } },
                    }),
                    Highlight,
                    TipTapImage.configure({ inline: false, allowBase64: false }),
                    TextAlign.configure({ types: ['heading', 'paragraph'] }),
                    Placeholder.configure({ placeholder: 'محتوای مقاله را اینجا بنویسید...' }),
                ],
                content: value,
                editorProps: {
                    attributes: {
                        class: 'tiptap-body min-h-[420px] p-5 focus:outline-none',
                        dir: 'rtl',
                    },
                },
                onUpdate({ editor }) {
                    if (self.$refs.hiddenContent) {
                        self.$refs.hiddenContent.value = editor.getHTML();
                    }
                },
                onTransaction() {
                    // Bump the reactive counter so Alpine re-evaluates toolbar :class bindings.
                    self.tick++;
                },
            });
        },

        destroy() {
            editor?.destroy();
            editor = null;
        },

        isActive(type, attrs) {
            void this.tick; // touch reactive dep so Alpine tracks selection changes
            return editor?.isActive(type, attrs) ?? false;
        },

        cmd(action) {
            if (!editor) return;
            const c = editor.chain().focus();
            const map = {
                bold:        () => c.toggleBold().run(),
                italic:      () => c.toggleItalic().run(),
                underline:   () => c.toggleUnderline().run(),
                strike:      () => c.toggleStrike().run(),
                highlight:   () => c.toggleHighlight().run(),
                h1:          () => c.toggleHeading({ level: 1 }).run(),
                h2:          () => c.toggleHeading({ level: 2 }).run(),
                h3:          () => c.toggleHeading({ level: 3 }).run(),
                bullet:      () => c.toggleBulletList().run(),
                ordered:     () => c.toggleOrderedList().run(),
                blockquote:  () => c.toggleBlockquote().run(),
                codeBlock:   () => c.toggleCodeBlock().run(),
                hr:          () => c.setHorizontalRule().run(),
                alignLeft:   () => c.setTextAlign('left').run(),
                alignCenter: () => c.setTextAlign('center').run(),
                alignRight:  () => c.setTextAlign('right').run(),
                undo:        () => c.undo().run(),
                redo:        () => c.redo().run(),
                link: () => {
                    const url = window.prompt('آدرس لینک را وارد کنید:');
                    if (url) c.setLink({ href: url }).run();
                    else c.unsetLink().run();
                },
                image: () => this.uploadImage(),
            };
            map[action]?.();
        },

        uploadImage() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async () => {
                const file = input.files[0];
                if (!file || !this.uploadUrl || !editor) return;
                const fd = new FormData();
                fd.append('image', file);
                const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const res = await fetch(this.uploadUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    body: fd,
                });
                const json = await res.json();
                if (json.url) editor.chain().focus().setImage({ src: json.url }).run();
            };
            input.click();
        },
    };
});

const SEEN_STORIES_KEY = 'seenStories';

Alpine.store('storyViewer', {
    isOpen: false,
    stories: [],
    storyIndex: 0,
    slideIndex: 0,
    duration: 5000,
    progress: 0,
    _interval: null,
    seen: JSON.parse(localStorage.getItem(SEEN_STORIES_KEY) || '[]'),

    setStories(stories) {
        this.stories = stories;
    },

    get currentStory() {
        return this.stories[this.storyIndex] || null;
    },

    get currentSlide() {
        return this.currentStory ? this.currentStory.slides[this.slideIndex] : null;
    },

    isSeen(id) {
        return this.seen.includes(id);
    },

    markSeen() {
        const id = this.currentStory?.id;
        if (id && !this.seen.includes(id)) {
            this.seen.push(id);
            localStorage.setItem(SEEN_STORIES_KEY, JSON.stringify(this.seen));
        }
    },

    open(index) {
        if (!this.stories[index]) return;
        this.storyIndex = index;
        this.slideIndex = 0;
        this.isOpen = true;
        this.markSeen();
        this._startProgress();
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.isOpen = false;
        this._stopProgress();
        document.body.style.overflow = '';
    },

    next() {
        const story = this.currentStory;
        if (!story) return this.close();

        if (this.slideIndex < story.slides.length - 1) {
            this.slideIndex++;
        } else if (this.storyIndex < this.stories.length - 1) {
            this.storyIndex++;
            this.slideIndex = 0;
            this.markSeen();
        } else {
            return this.close();
        }
        this._startProgress();
    },

    prev() {
        if (this.slideIndex > 0) {
            this.slideIndex--;
        } else if (this.storyIndex > 0) {
            this.storyIndex--;
            this.slideIndex = Math.max((this.currentStory?.slides.length ?? 1) - 1, 0);
        }
        this._startProgress();
    },

    _startProgress() {
        this._stopProgress();
        this.progress = 0;
        const tick = 50; // ms per tick
        const step = (tick / this.duration) * 100;
        this._interval = setInterval(() => {
            this.progress = Math.min(this.progress + step, 100);
            if (this.progress >= 100) {
                this._stopProgress();
                this.next();
            }
        }, tick);
    },

    _stopProgress() {
        if (this._interval) clearInterval(this._interval);
        this._interval = null;
    },
});

window.Alpine = Alpine;
Alpine.start();

if (window.jalaliDatepicker) {
    window.jalaliDatepicker.startWatch({
        minDate: 'attr',
        maxDate: 'attr',
    });
}
