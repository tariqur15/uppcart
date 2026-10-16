import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import TableUp, { defaultCustomSelect, TableMenuContextmenu, TableMenuSelect, TableResizeBox, TableResizeScale, TableSelection, TableVirtualScrollbar } from 'quill-table-up';
import 'quill-table-up/index.css';

Quill.register({ [`modules/${TableUp.moduleName}`]: TableUp }, true);

Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.store('cart', {
    count: 0,
    items: [],
    total: 0,
    drawerOpen: false,
    _closeTimer: null,

    openDrawer() {
        clearTimeout(this._closeTimer);
        this.drawerOpen = true;
        this.refresh();
    },

    scheduleClose() {
        this._closeTimer = setTimeout(() => { this.drawerOpen = false; }, 200);
    },

    cancelClose() {
        clearTimeout(this._closeTimer);
    },

    init() {
        this.count = Number(document.body.dataset.cartCount ?? 0);
        window.addEventListener('cart-updated', (e) => {
            this.count = e.detail.count ?? this.count;
            this.refresh();
        });
    },

    async refresh() {
        try {
            const resp = await fetch('/cart/drawer', { headers: { 'Accept': 'application/json' } });
            const data = await resp.json();
            if (data.success) {
                this.items = data.items;
                this.total = data.total;
                this.count = data.count;
            }
        } catch (err) {
            // Keep existing state on network failure
        }
    },

    toggleDrawer() {
        this.drawerOpen = !this.drawerOpen;
        if (this.drawerOpen) this.refresh();
    },

    async updateQty(item, qty) {
        if (!item || qty < 1 || qty > 99) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const url = item.is_session ? `/cart/session/${item.product_id}` : `/cart/${item.key}`;
        try {
            const resp = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ quantity: qty }),
            });
            const data = await resp.json();
            if (data.success) {
                await this.refresh();
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            }
        } catch (err) {
            // Keep existing state on network failure
        }
    },

    async removeItem(item) {
        if (!item) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const url = item.is_session ? `/cart/session/${item.product_id}` : `/cart/${item.key}`;
        try {
            const resp = await fetch(url, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            const data = await resp.json();
            if (data.success) {
                await this.refresh();
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            }
        } catch (err) {
            // Keep existing state on network failure
        }
    },
});

function flyToCart(fromEl, imageUrl) {
    const target = document.querySelector('[data-cart-icon]');
    if (!fromEl || !target) return;

    const startRect = fromEl.getBoundingClientRect();
    const endRect = target.getBoundingClientRect();
    const size = 50;

    function startAnimation(src) {
        const fly = document.createElement('div');
        fly.className = 'fly-cart-item';
        fly.style.cssText = `position:fixed;left:${startRect.left + startRect.width / 2 - size / 2}px;top:${startRect.top + startRect.height / 2 - size / 2}px;width:${size}px;height:${size}px;z-index:9999;pointer-events:none;overflow:hidden;border-radius:8px;border:2px solid white;box-shadow:0 4px 12px rgba(0,0,0,.25);background:#e5e7eb;`;
        if (src) {
            fly.innerHTML = `<img src="${src}" style="width:100%;height:100%;object-fit:cover;" alt="">`;
        } else {
            fly.innerHTML = '<div class="w-full h-full bg-indigo-600/80"></div>';
        }
        document.body.appendChild(fly);

        const startX = startRect.left + startRect.width / 2;
        const startY = startRect.top + startRect.height / 2;
        const endX = endRect.left + endRect.width / 2;
        const endY = endRect.top + endRect.height / 2;
        const cx = startX + (endX - startX) / 2 + 80;
        const cy = Math.min(startY, endY) - 140;

        const duration = 700;
        const startTime = performance.now();

        function frame(now) {
            const t = Math.min((now - startTime) / duration, 1);
            const u = 1 - t;
            const x = u * u * startX + 2 * u * t * cx + t * t * endX;
            const y = u * u * startY + 2 * u * t * cy + t * t * endY;

            fly.style.left = `${x}px`;
            fly.style.top = `${y}px`;
            fly.style.transform = `translate(-50%, -50%) scale(${1 - 0.6 * t})`;
            fly.style.opacity = String(1 - 0.4 * t);

            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                fly.remove();
                bumpCartIcon();
            }
        }

        requestAnimationFrame(frame);
    }

    if (imageUrl) {
        const img = new Image();
        img.onload = () => startAnimation(imageUrl);
        img.onerror = () => startAnimation(null);
        img.src = imageUrl;
    } else {
        startAnimation(null);
    }
}

function bumpCartIcon() {
    const icon = document.querySelector('[data-cart-icon]');
    if (!icon) return;
    icon.classList.remove('cart-bump');
    void icon.offsetWidth;
    icon.classList.add('cart-bump');
}

window.flyToCart = flyToCart;
window.bumpCartIcon = bumpCartIcon;

Alpine.data('variantModal', () => ({
    showModal: false,
    loading: false,
    qty: 1,
    selectedOptions: {},
    variantData: {},
    variantOptions: [],
    defaultPrice: 0,
    defaultStock: 0,
    productImage: '',
    productName: '',
    productId: null,

    init() {
        window.addEventListener('open-variant-modal', (e) => {
            const p = e.detail;
            this.productId = p.id;
            this.productName = p.name;
            this.productImage = p.image;
            this.variantData = p.variantData;
            this.variantOptions = p.variantOptions;
            this.defaultPrice = p.defaultPrice;
            this.defaultStock = p.defaultStock;
            this.selectedOptions = {};
            this.qty = 1;
            this.showModal = true;
        });
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.showModal = false;
        });
    },

    selectOption(name, value) {
        this.selectedOptions[name] = value;
    },
    isSelected(name, value) {
        return this.selectedOptions[name] === value;
    },
    getValueImage(optionIndex, value) {
        for (const key in this.variantData) {
            const parts = key.split(' / ');
            if (parts[optionIndex] === value && this.variantData[key]?.image) {
                return this.variantData[key].image;
            }
        }
        return null;
    },
    selectedVariantName() {
        const names = this.variantOptions.map(o => this.selectedOptions[o.name]).filter(Boolean);
        return names.length === this.variantOptions.length ? names.join(' / ') : '';
    },
    selectedVariant() {
        const name = this.selectedVariantName();
        return name ? (this.variantData[name] || null) : null;
    },
    activePrice() {
        const v = this.selectedVariant();
        return v && v.price ? v.price : this.defaultPrice;
    },
    activeStock() {
        const v = this.selectedVariant();
        return v && v.stock != null ? parseInt(v.stock) : this.defaultStock;
    },
    activeImage() {
        const v = this.selectedVariant();
        if (v && v.image) return v.image;
        return this.productImage;
    },
    canAddToCart() {
        return this.selectedVariantName() !== '' && this.activeStock() > 0;
    },
    addToCart() {
        if (!this.canAddToCart()) return;
        this.loading = true;
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: this.productId, quantity: this.qty, variant: this.selectedVariantName() })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const modalImg = this.$refs.modalImage;
                if (modalImg) flyToCart(modalImg, modalImg.src);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                window.dispatchEvent(new CustomEvent('cart-added', { detail: { count: data.cart_count } }));
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.productName + ' added to cart!', type: 'success' } }));
                this.showModal = false;
                this.selectedOptions = {};
                this.qty = 1;
            }
        })
        .finally(() => { this.loading = false; });
    },
    buyNow() {
        if (!this.canAddToCart()) return;
        this.loading = true;
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: this.productId, quantity: this.qty, variant: this.selectedVariantName() })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) window.location.href = '/checkout';
        })
        .finally(() => { this.loading = false; });
    },
}));

Alpine.data('liveSearch', () => ({
    query: '',
    results: [],
    resultsOpen: false,
    searched: false,
    search() {
        if (this.query.length < 2) {
            this.results = [];
            this.resultsOpen = false;
            this.searched = false;
            return;
        }
        fetch(`/products/search/ajax?q=${encodeURIComponent(this.query)}`)
            .then(r => r.json())
            .then(data => {
                this.results = data.products;
                this.resultsOpen = true;
                this.searched = true;
            })
            .catch(() => {
                this.results = [];
                this.resultsOpen = false;
            });
    },
    submitSearch() {
        if (this.query.trim()) {
            window.location.href = `/products?search=${encodeURIComponent(this.query)}`;
        }
    },
}));

Alpine.data('mediaManager', (params = null) => {
    let featuredUrl = params?.featuredUrl ?? null;
    let existingImages = params?.existingImages ?? [];

    if (!params && typeof document !== 'undefined') {
        const configEl = document.getElementById('media-config');
        if (configEl) {
            try {
                const config = JSON.parse(configEl.textContent);
                featuredUrl = config.featuredUrl ?? null;
                existingImages = config.existingImages ?? [];
            } catch (e) {
                console.warn('[mediaManager] Failed to parse config', e);
            }
        }
    }

    return {
    // --- Core State ---
    featuredPreview: featuredUrl,
    featuredWasSet: !!featuredUrl,
    removeFeaturedFlag: false,
    pendingGalleryFiles: [],

    init() {
        this.$nextTick(() => {
            const form = this.$el.querySelector('form');
            if (!form) return;

            form.addEventListener('submit', () => {
                const files = this.items.filter(i => i.file).map(i => i.file);
                if (files.length > 0) {
                    const dt = new DataTransfer();
                    for (const f of files) dt.items.add(f);
                    const input = document.getElementById('gallery-input');
                    if (input && dt.files.length > 0) {
                        input.files = dt.files;
                    }
                }
                const libItems = this.items.filter(i => i.fromLibrary && i.path);
                const featuredFromLib = this.featuredPreview && !this.featuredPreview.startsWith('blob:') && !this.featuredWasSet;
                const featuredPath = libItems.length > 0 && featuredFromLib ? libItems[0].path : null;
                if (featuredPath) {
                    let input = form.querySelector('[name="featured_library_path"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'featured_library_path';
                        form.appendChild(input);
                    }
                    input.value = featuredPath;
                }
                const galleryLibPaths = libItems.map(i => i.path).filter(Boolean);
                if (galleryLibPaths.length > 0) {
                    let input = form.querySelector('[name="gallery_library_paths"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'gallery_library_paths';
                        form.appendChild(input);
                    }
                    input.value = JSON.stringify(galleryLibPaths);
                }
            });
        });
    },
    items: existingImages.map((path, i) => ({
        id: `e:${i}`,
        path: path,
        url: `/storage/${path}`,
        isExisting: true,
        name: path.split('/').pop(),
        extension: path.split('.').pop().toUpperCase(),
    })),
    removedExisting: [],

    // --- Batch Selection ---
    selectedIndices: [],

    // --- Drag State ---
    dragIndex: null,

    // --- Add Menu ---
    showAddMenu: false,

    // --- Overflow Expand ---
    overflowExpanded: false,

    // --- Library Modal State ---
    showLibraryModal: false,
    libraryQuery: '',
    librarySort: 'newest',
    libraryView: 'grid',
    libraryItems: [],
    librarySelectedIndices: [],
    loadingLibrary: false,
    uploadingMedia: false,
    deletingMedia: false,

    // --- Computed ---
    get isManaging() {
        return this.selectedIndices.length > 0;
    },
    get isEmpty() {
        return !this.featuredPreview && this.items.length === 0;
    },
    get allSelected() {
        return this.items.length > 0 && this.selectedIndices.length === this.items.length;
    },
    get filteredLibraryItems() {
        let items = this.libraryItems;
        if (this.libraryQuery.trim()) {
            const q = this.libraryQuery.toLowerCase();
            items = items.filter(item => item.name.toLowerCase().includes(q));
        }
        if (this.librarySort === 'newest') {
            return items.slice().reverse();
        } else if (this.librarySort === 'oldest') {
            return items;
        } else if (this.librarySort === 'name') {
            return items.slice().sort((a, b) => a.name.localeCompare(b.name));
        }
        return items;
    },

    // ===================== FEATURED =====================

    setFeatured(file) {
        this.removeFeaturedFlag = false;
        if (this.featuredPreview && !this.featuredPreview.startsWith('/storage/')) {
            URL.revokeObjectURL(this.featuredPreview);
        }
        this.featuredPreview = URL.createObjectURL(file);
    },

    removeFeatured() {
        if (this.featuredPreview && !this.featuredPreview.startsWith('/storage/')) {
            URL.revokeObjectURL(this.featuredPreview);
        }
        this.featuredPreview = null;
        if (this.featuredWasSet) {
            this.removeFeaturedFlag = true;
        }
    },

    swapFeatured(index) {
        if (index < 0 || index >= this.items.length) return;
        const newFeatured = this.items[index];
        const newFeaturedUrl = newFeatured.url;
        const isExisting = newFeatured.isExisting;
        const path = newFeatured.path;

        this.items.splice(index, 1);

        if (this.featuredPreview) {
            this.items.unshift({
                id: `swapped-${Date.now()}`,
                path: this.featuredWasSet ? null : null,
                url: this.featuredPreview,
                isExisting: false,
                name: null,
                extension: null,
            });
        } else {
            this.featuredWasSet = false;
        }

        this.featuredPreview = newFeaturedUrl;

        if (isExisting && path) {
            this.featuredWasSet = true;
            this.removeFeaturedFlag = false;
            this.removedExisting = this.removedExisting.filter(p => p !== path);
        }
    },

    // ===================== GALLERY ITEMS =====================

    addFiles(files) {
        const newItems = [];
        for (const file of Array.from(files)) {
            if (!file.type.startsWith('image/')) continue;
            const item = {
                id: `n:${Date.now()}-${Math.random().toString(36).slice(2, 6)}`,
                file: file,
                url: URL.createObjectURL(file),
                isExisting: false,
                name: file.name,
                extension: file.name.split('.').pop().toUpperCase(),
            };
            newItems.push(item);
            this.items.push(item);
        }
        if (!this.featuredPreview && newItems.length > 0) {
            this.setFeatured(newItems[0].file);
        }
    },

    remove(index) {
        const item = this.items[index];
        if (!item) return;
        if (item.isExisting && item.path) {
            this.removedExisting.push(item.path);
        }
        if (!item.isExisting && item.file) {
            URL.revokeObjectURL(item.url);
        }
        this.items.splice(index, 1);
        this.selectedIndices = this.selectedIndices.filter(i => i !== index).map(i => i > index ? i - 1 : i);
    },

    removeSelected() {
        const sorted = [...this.selectedIndices].sort((a, b) => b - a);
        for (const idx of sorted) {
            const item = this.items[idx];
            if (item.isExisting && item.path) {
                this.removedExisting.push(item.path);
            }
            if (!item.isExisting && item.file) {
                URL.revokeObjectURL(item.url);
            }
            this.items.splice(idx, 1);
        }
        this.selectedIndices = [];
    },

    toggleSelect(index) {
        const pos = this.selectedIndices.indexOf(index);
        if (pos === -1) {
            this.selectedIndices.push(index);
        } else {
            this.selectedIndices.splice(pos, 1);
        }
    },

    toggleSelectAll() {
        if (this.allSelected) {
            this.selectedIndices = [];
        } else {
            this.selectedIndices = this.items.map((_, i) => i);
        }
    },

    clearSelection() {
        this.selectedIndices = [];
    },

    // ===================== DRAG & DROP =====================

    dragStart(index) {
        this.dragIndex = index;
    },

    dragOver(e) {
        e.preventDefault();
    },

    drop(index) {
        if (this.dragIndex === null || this.dragIndex === index) {
            this.dragIndex = null;
            return;
        }

        if (index === -1) {
            this.swapFeatured(this.dragIndex);
            this.dragIndex = null;
            return;
        }

        const item = this.items.splice(this.dragIndex, 1)[0];
        const adjustedIndex = index < this.dragIndex ? index : index;
        this.items.splice(adjustedIndex, 0, item);
        this.dragIndex = null;
    },

    // ===================== ORDER TRACKING =====================

    getOrder() {
        return this.items.map(item => item.id).join(',');
    },

    // ===================== UPLOAD =====================

    triggerGalleryUpload() {
        this.showAddMenu = false;
        this.$nextTick(() => {
            document.getElementById('gallery-input')?.click();
        });
    },

    handleDropUpload(e) {
        const files = e.dataTransfer?.files;
        if (files && files.length > 0) {
            this.addFiles(files);
        }
    },

    // ===================== LIBRARY MODAL =====================

    openLibraryModal() {
        this.showAddMenu = false;
        this.showLibraryModal = true;
        this.librarySelectedIndices = [];
        this.fetchLibraryItems();
    },

    closeLibraryModal() {
        this.showLibraryModal = false;
        this.libraryQuery = '';
        this.librarySelectedIndices = [];
    },

    async fetchLibraryItems() {
        if (this.loadingLibrary) return;
        this.loadingLibrary = true;
        try {
            const resp = await fetch('/admin/media/recent');
            const data = await resp.json();
            this.libraryItems = (data.images ?? []).map(img => ({
                url: img.url,
                path: img.path,
                name: img.name,
                extension: img.extension,
            }));
        } catch {
            this.libraryItems = [];
        }
        this.loadingLibrary = false;
    },

    toggleLibrarySelect(index) {
        const pos = this.librarySelectedIndices.indexOf(index);
        if (pos === -1) {
            this.librarySelectedIndices.push(index);
        } else {
            this.librarySelectedIndices.splice(pos, 1);
        }
    },

    clearLibrarySelection() {
        this.librarySelectedIndices = [];
    },

    confirmLibrarySelection() {
        const newItems = [];
        for (const idx of this.librarySelectedIndices) {
            const item = this.filteredLibraryItems[idx];
            newItems.push({
                id: `lib:${Date.now()}-${Math.random().toString(36).slice(2, 6)}`,
                url: item.url,
                path: item.path || '',
                isExisting: false,
                fromLibrary: true,
                name: item.name,
                extension: item.extension,
            });
            this.items.push(newItems[newItems.length - 1]);
        }
        if (!this.featuredPreview && newItems.length > 0) {
            const img = new Image();
            img.onload = () => {
                if (URL.revokeObjectURL) URL.revokeObjectURL(img.src);
            };
            this.featuredPreview = newItems[0].url;
            this.removeFeaturedFlag = false;
        }
        this.closeLibraryModal();
    },

    async deleteSelectedLibraryItems() {
        const indices = this.librarySelectedIndices;
        if (indices.length === 0) return;
        if (!confirm(`Delete ${indices.length} selected image(s)?`)) return;
        const paths = indices.map(idx => this.filteredLibraryItems[idx].path);
        const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
        this.deletingMedia = true;
        await Promise.all(paths.map(path =>
            fetch('/admin/media/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ path }),
            }).catch(() => {})
        ));
        this.deletingMedia = false;
        this.libraryItems = this.libraryItems.filter(i => !paths.includes(i.path));
        this.librarySelectedIndices = [];
    },

    async uploadFromLibraryDropzone(files) {
        const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
        if (imageFiles.length === 0) return;
        this.uploadingMedia = true;
        const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
        await Promise.all(imageFiles.map(async (file) => {
            const fd = new FormData();
            fd.append('file', file);
            try {
                await fetch('/admin/media/upload', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': csrf } });
            } catch {
                // Silently skip failed uploads
            }
        }));
        this.uploadingMedia = false;
        this.libraryQuery = '';
        await this.fetchLibraryItems();
    },
};
});

Alpine.data('variantManager', ({ enabled: initialEnabled = false, options: initialOptions = [], variantData: initialVariantData = {}, productType: initialProductType = 'physical' } = {}) => ({
    enabled: initialEnabled,
    productType: initialProductType,
    options: initialOptions.map(o => ({ ...o, errors: { name: false }, done: true })),
    variantData: Array.isArray(initialVariantData) ? {} : initialVariantData,
    groupBy: '',
    dragging: false,
    dragOverValue: null,
    searchQuery: '',
    bulkEditOpen: false,
    bulkData: { price: '', salePrice: '', stock: '', sku: '' },
    showVariantLibrary: false,
    variantLibraryTarget: null,
    variantLibraryGroupChildren: null,
    variantLibraryQuery: '',
    variantLibrarySort: 'newest',
    variantLibraryView: 'grid',
    variantLibraryItems: [],
    variantLibrarySelectedIndices: [],
    loadingVariantLibrary: false,
    uploadingVariantMedia: false,
    deletingVariantMedia: false,

    init() {
        this.$nextTick(() => {
            const form = this.$el.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    this.serializeToForm(form);
                });
            }
        });
        if (this.options.length > 0) {
            const done = this.options.filter(o => o.done && o.name.trim());
            if (done.length > 0) this.groupBy = done[0].name;
        }
        this.$watch('enabled', (val) => {
            if (val && this.options.length === 0) this.addOption();
        });
        this.$watch('options', () => {
            const done = this.options.filter(o => o.done && o.name.trim());
            if (done.length > 0 && (!this.groupBy || !done.some(o => o.name === this.groupBy))) {
                this.groupBy = done[0].name;
            }
        }, { deep: true });
        this.$watch('productType', (val) => {
            this.enabled = (val === 'variable');
            this.$dispatch('product-type-changed', { type: val });
        });
        this.$nextTick(() => {
            this.$dispatch('product-type-changed', { type: this.productType });
        });
    },

    serializeToForm(form) {
        let field = form.querySelector('[name="variants_enabled"]');
        if (!field) {
            field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'variants_enabled';
            form.appendChild(field);
        }
        field.value = this.enabled ? '1' : '0';

        const optData = this.options.map(o => ({
            name: o.name,
            values: o.values.filter(v => v.trim() !== ''),
        }));

        field = form.querySelector('[name="variant_options"]');
        if (!field) {
            field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'variant_options';
            form.appendChild(field);
        }
        field.value = JSON.stringify(optData);

        field = form.querySelector('[name="variant_data"]');
        if (!field) {
            field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'variant_data';
            form.appendChild(field);
        }
        field.value = JSON.stringify(this.variantData);
    },

    get generatedVariants() {
        const doneOptions = this.options.filter(o => o.done && o.values.some(v => v.trim()));
        if (doneOptions.length === 0) return [];

        const valueArrays = doneOptions.map(o => o.values.filter(v => v.trim()));

        function cartesian(arrays) {
            if (arrays.length === 0) return [[]];
            const [first, ...rest] = arrays;
            return first.flatMap(val => cartesian(rest).map(c => [val, ...c]));
        }

        const defaultPrice = document.getElementById('price')?.value ?? '';
        const defaultStock = document.getElementById('stock')?.value ?? '';
        const defaultSku = document.getElementById('sku')?.value ?? '';

        return cartesian(valueArrays).map((combo, i) => {
            const name = combo.join(' / ');
            const saved = this.variantData[name] || {};
            return {
                id: `v-${i}`,
                name,
                attrs: [...combo],
                selected: saved.selected ?? false,
                image: saved.image ?? '',
                sku: saved.sku !== undefined && saved.sku !== '' ? saved.sku : defaultSku,
                price: saved.price !== undefined && saved.price !== '' ? saved.price : defaultPrice,
                stock: saved.stock !== undefined && saved.stock !== '' ? saved.stock : defaultStock,
            };
        });
    },

    get groupedVariants() {
        const variants = this.generatedVariants;
        if (variants.length === 0) return [];

        const doneOpts = this.options.filter(o => o.done && o.name.trim());
        if (doneOpts.length === 0) return [];

        const groupAttr = this.groupBy || doneOpts[0].name;
        const groupIdx = doneOpts.findIndex(o => o.name === groupAttr);
        if (groupIdx === -1) return [];

        const grouped = {};
        for (const v of variants) {
            const key = v.attrs[groupIdx];
            if (!grouped[key]) grouped[key] = { value: key, children: [] };
            grouped[key].children.push(v);
        }

        return Object.values(grouped).map(g => ({
            value: g.value,
            count: g.children.length,
            children: g.children,
        }));
    },

    getChildLabel(child) {
        const doneOpts = this.options.filter(o => o.done && o.name.trim());
        const groupAttr = this.groupBy || (doneOpts[0]?.name ?? '');
        const groupIdx = doneOpts.findIndex(o => o.name === groupAttr);
        if (groupIdx === -1) return child.name;
        return child.attrs.filter((_, idx) => idx !== groupIdx).join(' / ');
    },

    get allSelected() {
        const all = this.generatedVariants;
        return all.length > 0 && all.every(v => v.selected);
    },

    get numSelected() {
        return this.generatedVariants.filter(v => v.selected).length;
    },

    toggleAllSelect() {
        const newVal = !this.allSelected;
        for (const v of this.generatedVariants) {
            if (!this.variantData[v.name]) this.variantData[v.name] = {};
            this.variantData[v.name].selected = newVal;
        }
    },

    addOption() {
        this.options.push({
            id: Date.now(),
            name: '',
            values: [''],
            errors: { name: false },
            done: false,
        });
        this.$nextTick(() => {
            const container = this.$el.closest('[x-data="variantManager"]');
            if (container) {
                const inputs = container.querySelectorAll('.variant-name-input');
                if (inputs.length > 0) inputs[inputs.length - 1].focus();
            }
        });
    },

    importFromAttributes() {
        // Attribute-based variant import is not supported in the current schema.
    },

    swapOptions(from, to) {
        if (from === to || to < 0 || to >= this.options.length) return;
        const temp = this.options[from];
        this.options[from] = this.options[to];
        this.options[to] = temp;
    },

    swapValues(optIndex, from, to) {
        if (from === to || to < 0) return;
        const opt = this.options[optIndex];
        if (!opt || to >= opt.values.length) return;
        const temp = opt.values[from];
        opt.values[from] = opt.values[to];
        opt.values[to] = temp;
    },

    handleDragStart(event, type, index, optIndex) {
        event.dataTransfer.setData('text/plain', JSON.stringify({ type, index, optIndex }));
        event.dataTransfer.effectAllowed = 'move';
        this.dragging = true;
    },

    handleDrop(event, type, index) {
        event.preventDefault();
        const data = JSON.parse(event.dataTransfer.getData('text/plain'));
        if (data.type === 'option' && type === 'option') {
            this.swapOptions(data.index, index);
        } else if (data.type === 'value' && type === 'value' && data.optIndex !== undefined) {
            this.swapValues(data.optIndex, data.index, index);
        }
        this.dragging = false;
        this.dragOverValue = null;
    },

    handleDragOver(type, optIndex, valIndex) {
        if (type === 'value') {
            this.dragOverValue = { optIndex, valIndex };
        }
    },

    clearDragOver() {
        this.dragOverValue = null;
    },

    removeOption(index) {
        this.options.splice(index, 1);
    },

    validateOption(index) {
        const opt = this.options[index];
        opt.errors.name = !opt.name.trim();
        opt.values = opt.values.filter(v => v.trim() !== '');
        if (opt.values.length === 0) opt.values.push('');
        if (!opt.errors.name) opt.done = true;
    },

    editOption(index) {
        this.options[index].done = false;
    },

    handleValueInput(optIndex, valIndex, e) {
        const opt = this.options[optIndex];
        opt.values[valIndex] = e.target.value;

        if (valIndex === opt.values.length - 1 && e.target.value.trim() !== '') {
            opt.values.push('');
        }

        if (e.target.value === '' && valIndex < opt.values.length - 1) {
            const lastFilled = [...opt.values].reverse().find(v => v.trim() !== '');
            const lastIdx = lastFilled ? opt.values.lastIndexOf(lastFilled) : -1;
            if (valIndex > lastIdx) opt.values.splice(valIndex, 1);
        }
    },

    removeValue(optIndex, valIndex) {
        const opt = this.options[optIndex];
        if (opt.values.length > 1) opt.values.splice(valIndex, 1);
    },

    updateVariantData(name, field, value) {
        if (!this.variantData[name]) this.variantData[name] = {};
        this.variantData[name][field] = value;
    },

    // Bulk edit
    openBulkEdit() {
        this.bulkData = { price: '', salePrice: '', stock: '', sku: '' };
        this.bulkEditOpen = true;
    },

    applyBulkEdit() {
        const selected = this.generatedVariants.filter(v => v.selected);
        for (const v of selected) {
            if (this.bulkData.price !== '') this.updateVariantData(v.name, 'price', this.bulkData.price);
            if (this.bulkData.stock !== '') this.updateVariantData(v.name, 'stock', this.bulkData.stock);
            if (this.bulkData.sku !== '') this.updateVariantData(v.name, 'sku', this.bulkData.sku);
        }
        this.bulkEditOpen = false;
    },

    // Variant library modal (mirrors media section library)
    get filteredVariantLibraryItems() {
        let items = this.variantLibraryItems;
        if (this.variantLibraryQuery.trim()) {
            const q = this.variantLibraryQuery.toLowerCase();
            items = items.filter(item => item.name.toLowerCase().includes(q));
        }
        if (this.variantLibrarySort === 'newest') {
            return items.slice().reverse();
        } else if (this.variantLibrarySort === 'oldest') {
            return items;
        } else if (this.variantLibrarySort === 'name') {
            return items.slice().sort((a, b) => a.name.localeCompare(b.name));
        }
        return items;
    },

    openVariantLibrary(name) {
        this.variantLibraryTarget = name;
        this.variantLibraryGroupChildren = null;
        this.showVariantLibrary = true;
        this.variantLibrarySelectedIndices = [];
        this.fetchVariantLibraryItems();
    },

    openVariantLibraryForGroup(grp) {
        this.variantLibraryTarget = grp.children[0].name;
        this.variantLibraryGroupChildren = grp.children;
        this.showVariantLibrary = true;
        this.variantLibrarySelectedIndices = [];
        this.fetchVariantLibraryItems();
    },

    closeVariantLibrary() {
        this.showVariantLibrary = false;
        this.variantLibraryQuery = '';
        this.variantLibrarySelectedIndices = [];
        this.variantLibraryGroupChildren = null;
    },

    async fetchVariantLibraryItems() {
        if (this.loadingVariantLibrary) return;
        this.loadingVariantLibrary = true;
        try {
            const resp = await fetch('/admin/media/recent');
            const data = await resp.json();
            this.variantLibraryItems = (data.images ?? []).map(img => ({
                url: img.url,
                path: img.path,
                name: img.name,
                extension: img.extension,
            }));
        } catch {
            this.variantLibraryItems = [];
        }
        this.loadingVariantLibrary = false;
    },

    toggleVariantLibrarySelect(idx) {
        const pos = this.variantLibrarySelectedIndices.indexOf(idx);
        if (pos === -1) {
            this.variantLibrarySelectedIndices.push(idx);
        } else {
            this.variantLibrarySelectedIndices.splice(pos, 1);
        }
    },

    confirmVariantLibrarySelection() {
        if (this.variantLibrarySelectedIndices.length > 0) {
            const idx = this.variantLibrarySelectedIndices[0];
            const item = this.filteredVariantLibraryItems[idx];
            if (item && this.variantLibraryTarget) {
                const targets = this.variantLibraryGroupChildren || [{ name: this.variantLibraryTarget }];
                targets.forEach(t => this.updateVariantData(t.name || t, 'image', item.url));
            }
        }
        this.closeVariantLibrary();
    },

    async uploadVariantFromDropzone(files) {
        const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
        if (imageFiles.length === 0) return;
        this.uploadingVariantMedia = true;
        const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
        await Promise.all(imageFiles.map(async (file) => {
            const fd = new FormData();
            fd.append('file', file);
            try {
                await fetch('/admin/media/upload', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': csrf } });
            } catch {
                // Silently skip failed uploads
            }
        }));
        this.uploadingVariantMedia = false;
        this.variantLibraryQuery = '';
        await this.fetchVariantLibraryItems();
    },

    async deleteSelectedVariantLibraryItems() {
        const indices = this.variantLibrarySelectedIndices;
        if (indices.length === 0) return;
        if (!confirm(`Delete ${indices.length} selected image(s)?`)) return;
        const paths = indices.map(idx => this.filteredVariantLibraryItems[idx].path);
        const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
        this.deletingVariantMedia = true;
        await Promise.all(paths.map(path =>
            fetch('/admin/media/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ path }),
            }).catch(() => {})
        ));
        this.deletingVariantMedia = false;
        this.variantLibraryItems = this.variantLibraryItems.filter(i => !paths.includes(i.path));
        this.variantLibrarySelectedIndices = [];
    },
}));

Alpine.data('tagPicker', ({ allTags = [], selectedTagIds = [], frequentlyUsedTags = [] } = {}) => ({
    allTags: allTags,
    selectedTags: allTags.filter(t => selectedTagIds.includes(t.id)),
    frequentlyUsedTags: frequentlyUsedTags,

    showModal: false,
    searchQuery: '',

    get frequentTagIds() {
        return this.frequentlyUsedTags.map(t => t.id);
    },

    get filteredFrequentTags() {
        const q = this.searchQuery.toLowerCase();
        return this.frequentlyUsedTags.filter(t =>
            !q || t.name.toLowerCase().includes(q)
        );
    },

    get filteredAllTags() {
        const freqIds = this.frequentTagIds;
        const q = this.searchQuery.toLowerCase();
        return this.allTags.filter(t =>
            !freqIds.includes(t.id) &&
            (!q || t.name.toLowerCase().includes(q))
        );
    },

    get canCreate() {
        const q = this.searchQuery.trim();
        if (!q) return false;
        return !this.allTags.some(t => t.name.toLowerCase() === q.toLowerCase());
    },

    isSelected(tagId) {
        return this.selectedTags.some(t => t.id === tagId);
    },

    toggleTag(tag) {
        const idx = this.selectedTags.findIndex(t => t.id === tag.id);
        if (idx === -1) {
            this.selectedTags.push(tag);
        } else {
            this.selectedTags.splice(idx, 1);
        }
    },

    removeTag(tagId) {
        this.selectedTags = this.selectedTags.filter(t => t.id !== tagId);
    },

    createAndAdd() {
        const name = this.searchQuery.trim();
        if (!name) return;
        const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
        fetch('/admin/tags/quick-create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ name }),
        })
            .then(r => r.json())
            .then(tag => {
                this.allTags.push(tag);
                this.selectedTags.push(tag);
                this.searchQuery = '';
            })
            .catch(() => {});
    },
}));

function createTableGridSelector(tableModule) {
    const MAX = 10;

    const wrapper = document.createElement('div');
    wrapper.className = 'table-grid-picker';
    wrapper.style.cssText = 'position:absolute;top:calc(100% + 2px);left:0;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);padding:12px;z-index:9999;display:none;width:auto;';

    const label = document.createElement('div');
    label.style.cssText = 'font-size:12px;color:#6b7280;margin-bottom:8px;text-align:center;min-width:80px;height:16px;font-family:system-ui,-apple-system,sans-serif;';
    label.textContent = 'Insert table';

    const grid = document.createElement('div');
    grid.style.cssText = `display:grid;grid-template-columns:repeat(${MAX},22px);grid-template-rows:repeat(${MAX},22px);gap:2px;`;

    for (let r = 0; r < MAX; r++) {
        for (let c = 0; c < MAX; c++) {
            const cell = document.createElement('div');
            cell.style.cssText = 'width:22px;height:22px;border:1px solid #d1d5db;border-radius:3px;background:#f9fafb;cursor:pointer;transition:background .06s,border-color .06s;';
            cell.dataset.row = r;
            cell.dataset.col = c;

            cell.addEventListener('mouseenter', () => {
                label.textContent = `${r + 1} × ${c + 1}`;
                grid.querySelectorAll('div').forEach((el) => {
                    const cr = parseInt(el.dataset.row);
                    const cc = parseInt(el.dataset.col);
                    if (cr <= r && cc <= c) {
                        el.style.background = '#6366f1';
                        el.style.borderColor = '#4f46e5';
                    } else {
                        el.style.background = '#f9fafb';
                        el.style.borderColor = '#d1d5db';
                    }
                });
            });

            grid.addEventListener('mouseleave', () => {
                label.textContent = 'Insert table';
                grid.querySelectorAll('div').forEach((el) => {
                    el.style.background = '#f9fafb';
                    el.style.borderColor = '#d1d5db';
                });
            });

            cell.addEventListener('click', () => {
                wrapper.style.display = 'none';
                tableModule.insertTable(r + 1, c + 1);
            });

            grid.appendChild(cell);
        }
    }

    wrapper.appendChild(label);
    wrapper.appendChild(grid);

    return wrapper;
}

function addToolbarTooltips(toolbar) {
    const buttonTips = {
        'ql-bold': 'Bold (Ctrl+B)',
        'ql-italic': 'Italic (Ctrl+I)',
        'ql-underline': 'Underline (Ctrl+U)',
        'ql-strike': 'Strikethrough',
        'ql-blockquote': 'Blockquote',
        'ql-code-block': 'Code block',
        'ql-link': 'Insert link',
        'ql-image': 'Insert image',
        'ql-video': 'Insert video',
        'ql-formula': 'Insert formula',
        'ql-clean': 'Clear formatting',
        'ql-html-source': 'HTML source view',
        'ql-ordered': 'Ordered list',
        'ql-bullet': 'Bullet list',
        'ql-indent ql--indent-1': 'Indent left',
        'ql-indent ql--indent+1': 'Indent right',
        'ql-script ql--super': 'Superscript',
        'ql-script ql--sub': 'Subscript',
    };

    toolbar.container.querySelectorAll('button[class]').forEach((btn) => {
        for (const [cls, tip] of Object.entries(buttonTips)) {
            const classes = cls.split(' ');
            if (classes.every((c) => btn.classList.contains(c))) {
                btn.setAttribute('title', tip);
                return;
            }
        }
    });

    const pickerTips = {
        'ql-header': 'Heading',
        'ql-font': 'Font family',
        'ql-color': 'Text color',
        'ql-background': 'Highlight color',
        'ql-align': 'Text alignment',
        'ql-direction': 'Text direction',
        'ql-table-up': 'Insert table',
    };

    toolbar.container.querySelectorAll('.ql-picker').forEach((picker) => {
        for (const [cls, tip] of Object.entries(pickerTips)) {
            if (picker.classList.contains(cls)) {
                const label = picker.querySelector('.ql-picker-label');
                if (label) label.setAttribute('title', tip);
                return;
            }
        }
    });
}

Alpine.data('richEditor', () => ({
    init() {
        this.$nextTick(() => {
            const input = document.getElementById(this.$el.dataset.input);
            const container = this.$el;

            const quill = new Quill(container, {
                theme: 'snow',
                placeholder: container.dataset.placeholder || 'Write your full product description...',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'font': [] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                            [{ 'align': [] }],
                            ['blockquote'],
                            [{ [TableUp.toolName]: [] }],
                            ['link', 'image', 'video'],
                            ['clean'],
                        ],
                        handlers: {},
                    },
                    [TableUp.moduleName]: {
                        full: false,
                        fullSwitch: true,
                        modules: [
                            { module: TableVirtualScrollbar },
                            { module: TableResizeScale },
                            { module: TableSelection },
                            { module: TableMenuContextmenu },
                            { module: TableMenuSelect },
                        ],
                    },
                },
            });

            const tableMod = quill.getModule(TableUp.moduleName);

            const createAlignSvg = (align) => {
                const lines = {
                    left: [[3,9,15,9],[3,14,13,14],[3,4,9,4]],
                    center: [[3,9,15,9],[4,14,14,14],[6,4,12,4]],
                    right: [[3,9,15,9],[5,14,15,14],[9,4,15,4]],
                };
                return () => {
                    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.setAttribute('viewBox', '0 0 18 18');
                    svg.setAttribute('width', '18');
                    svg.setAttribute('height', '18');
                    for (const [x1,y1,x2,y2] of lines[align]) {
                        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        line.setAttribute('x1', x1);
                        line.setAttribute('y1', y1);
                        line.setAttribute('x2', x2);
                        line.setAttribute('y2', y2);
                        line.setAttribute('stroke', '#444');
                        line.setAttribute('stroke-width', '1.5');
                        line.setAttribute('stroke-linecap', 'round');
                        svg.appendChild(line);
                    }
                    return svg;
                };
            };

            const setTableColAlign = (tableModule, selectedTds, align) => {
                if (!selectedTds.length) return;
                const td = selectedTds[0];
                const tableNode = td.domNode.closest('table');
                if (!tableNode) return;
                const tableBlot = Quill.find(tableNode);
                if (tableBlot && typeof tableBlot.getCols === 'function') {
                    for (const col of tableBlot.getCols()) {
                        col.align = align;
                    }
                }
            };

            const alignBreak = { name: 'break' };
            const alignLeftTool = {
                name: 'AlignLeft',
                icon: createAlignSvg('left'),
                tip: 'Align left',
                handle(tableModule, selectedTds) {
                    setTableColAlign(tableModule, selectedTds, 'left');
                },
            };
            const alignCenterTool = {
                name: 'AlignCenter',
                icon: createAlignSvg('center'),
                tip: 'Align center',
                handle(tableModule, selectedTds) {
                    setTableColAlign(tableModule, selectedTds, 'center');
                },
            };
            const alignRightTool = {
                name: 'AlignRight',
                icon: createAlignSvg('right'),
                tip: 'Align right',
                handle(tableModule, selectedTds) {
                    setTableColAlign(tableModule, selectedTds, 'right');
                },
            };
            const alignTools = [alignBreak, alignLeftTool, alignCenterTool, alignRightTool];

            const menuSelect = tableMod.getModule('table-menu-select');
            if (menuSelect) {
                menuSelect.options.tools.push(...alignTools);
            }
            const menuCtx = tableMod.getModule('table-menu-contextmenu');
            if (menuCtx) {
                menuCtx.options.tools.push(...alignTools);
            }

            const gridPicker = createTableGridSelector(tableMod);

            const toolbar = quill.getModule('toolbar');
            addToolbarTooltips(toolbar);

            const tableBtn = toolbar.container.querySelector('.ql-table-up, .ql-picker.ql-table-up');
            if (tableBtn) {
                tableBtn.style.position = 'relative';
                tableBtn.appendChild(gridPicker);

                const label = tableBtn.querySelector('.ql-picker-label');
                if (label) {
                    label.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const visible = gridPicker.style.display !== 'none';
                        gridPicker.style.display = visible ? 'none' : 'block';
                    });
                }

                document.addEventListener('click', (e) => {
                    if (!gridPicker.contains(e.target) && !tableBtn.contains(e.target)) {
                        gridPicker.style.display = 'none';
                    }
                });
            }

            const qlToolbar = toolbar.container;
            const sourceBtn = document.createElement('button');
            sourceBtn.type = 'button';
            sourceBtn.className = 'ql-html-source';
            sourceBtn.setAttribute('title', 'HTML source view');
            sourceBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>';

            const sourceGroup = document.createElement('span');
            sourceGroup.className = 'ql-formats';
            sourceGroup.appendChild(sourceBtn);
            qlToolbar.appendChild(sourceGroup);

            let isSourceMode = false;
            let sourceOverlay = null;

            sourceBtn.addEventListener('click', () => {
                if (!isSourceMode) {
                    const html = quill.root.innerHTML;
                    quill.root.style.display = 'none';

                    sourceOverlay = document.createElement('textarea');
                    sourceOverlay.value = html;
                    sourceOverlay.style.cssText = 'width:100%;min-height:400px;padding:16px;font-family:Consolas,Monaco,"Courier New",monospace;font-size:13px;line-height:1.6;color:#e2e8f0;background:#1e293b;border:1px solid #334155;border-radius:6px;resize:vertical;tab-size:2;outline:none;';
                    container.appendChild(sourceOverlay);
                    sourceOverlay.focus();
                    isSourceMode = true;
                    sourceBtn.classList.add('ql-active');
                } else {
                    const raw = sourceOverlay.value;
                    quill.root.innerHTML = raw;
                    quill.root.style.display = '';
                    sourceOverlay.remove();
                    sourceOverlay = null;
                    isSourceMode = false;
                    sourceBtn.classList.remove('ql-active');
                }
            });

            quill.on('text-change', () => {
                if (input) {
                    input.value = quill.root.innerHTML;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }

                const counter = container.closest('.rich-editor-wrapper')?.parentElement?.querySelector('.char-counter');
                if (counter) {
                    const text = quill.getText().trim();
                    const max = parseInt(container.dataset.maxlength, 10);
                    counter.textContent = text.length + (max ? ' / ' + max : '');
                    counter.style.color = (max && text.length > max) ? '#dc2626' : '';
                }
            });

            if (input && input.value) {
                quill.root.innerHTML = input.value;

                const counter = container.closest('.rich-editor-wrapper')?.parentElement?.querySelector('.char-counter');
                if (counter) {
                    const text = quill.getText().trim();
                    const max = parseInt(container.dataset.maxlength, 10);
                    counter.textContent = text.length + (max ? ' / ' + max : '');
                    counter.style.color = (max && text.length > max) ? '#dc2626' : '';
                }
            }

            const qlContainer = container.closest('.ql-container') || container.parentElement;
            const mainToolbar = qlContainer?.previousElementSibling;
            if (mainToolbar && mainToolbar.classList.contains('ql-toolbar')) {
                const MIN_GAP = 8;
                let constrainFrame = false;

                const constrainFloatingElements = () => {
                    if (constrainFrame) return;
                    constrainFrame = true;

                    requestAnimationFrame(() => {
                        const toolbarBottom = mainToolbar.getBoundingClientRect().bottom;
                        const minY = toolbarBottom + MIN_GAP;

                        const selectors = '.table-up-menu:not(.is-hidden)';
                        new Set([
                            ...qlContainer.querySelectorAll(selectors),
                            ...container.querySelectorAll(selectors),
                        ]).forEach((el) => {
                            const rect = el.getBoundingClientRect();
                            if (rect.top < minY) {
                                const currentTop = parseFloat(el.style.top) || 0;
                                el.style.top = `${currentTop + (minY - rect.top)}px`;
                            }
                        });

                        constrainFrame = false;
                    });
                };

                const observer = new MutationObserver(constrainFloatingElements);
                observer.observe(qlContainer, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['style', 'class'],
                });
                quill.on('selection-change', constrainFloatingElements);
            }
        });
    },
}));

Alpine.data('globalSearch', () => ({
    query: '',
    open: false,
    searched: false,
    timer: null,
    results: { products: [], categories: [], orders: [] },
    get hasResults() {
        return (this.results?.products?.length ?? 0) > 0
            || (this.results?.categories?.length ?? 0) > 0
            || (this.results?.orders?.length ?? 0) > 0;
    },
    search() {
        if (this.timer) clearTimeout(this.timer);
        const q = this.query.trim();
        if (!q) {
            this.searched = false;
            this.results = { products: [], categories: [], orders: [] };
            this.open = false;
            return;
        }
        this.timer = setTimeout(() => {
            fetch('/admin/search?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.results = data;
                this.searched = true;
                this.open = true;
            });
        }, 100);
    },
}));

Alpine.data('chatComposer', () => ({
    message: '',
    sending: false,
    error: '',

    init() {
        this.thread = document.getElementById('messageThread');
        if (this.thread) {
            this.thread.scrollTop = this.thread.scrollHeight;
        }

        const attachmentInput = document.getElementById('attachment');
        attachmentInput?.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            document.getElementById('attachmentChip')?.remove();
            if (!file) return;
            const chip = document.createElement('span');
            chip.id = 'attachmentChip';
            chip.className = 'inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full mb-1';
            chip.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg> ${file.name} <button type="button" class="text-indigo-400 hover:text-indigo-600" onclick="this.closest('span').remove()">&times;</button>`;
            this.$el.insertBefore(chip, this.$el.firstChild);
        });
    },

    submit() {
        const fileInput = document.getElementById('attachment');
        const hasMessage = this.message.trim().length > 0;
        const hasFile = fileInput?.files?.length > 0;

        if (!hasMessage && !hasFile) {
            this.$refs.messageTextarea?.focus();
            return;
        }

        this.sending = true;
        this.error = '';

        const fd = new FormData();
        if (hasMessage) fd.append('message', this.message.trim());
        if (hasFile) fd.append('attachment', fileInput.files[0]);
        fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content ?? '');

        fetch(this.$el.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
            .then(async (resp) => {
                const data = await resp.json().catch(() => ({}));
                if (!resp.ok) {
                    this.error = data.errors?.message?.[0] || data.message || 'Failed to send message.';
                    return;
                }
                this.appendBubble(data);
                this.message = '';
                if (fileInput) fileInput.value = '';
                document.getElementById('attachmentChip')?.remove();
                this.scrollToBottom();
            })
            .catch(() => {
                this.error = 'Network error. Please try again.';
            })
            .finally(() => {
                this.sending = false;
            });
    },

    appendBubble(msg) {
        const empty = document.getElementById('messageEmptyState');
        empty?.remove();

        const wrapper = document.createElement('div');
        wrapper.className = 'flex justify-end';

        const bubble = document.createElement('div');
        bubble.className = 'max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-br-md px-3.5 py-2';

        if (msg.attachment) {
            const link = document.createElement('a');
            link.href = msg.attachment.download_url;
            link.target = '_blank';
            link.className = 'block mb-1.5';
            const inner = document.createElement('div');
            inner.className = 'flex items-center gap-2.5 bg-indigo-500/40 rounded-lg px-3 py-2';
            if (msg.attachment.mime?.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = msg.attachment.url;
                img.alt = msg.attachment.name;
                img.className = 'max-w-[200px] max-h-[160px] rounded-lg object-contain';
                inner.appendChild(img);
            } else {
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', '18');
                svg.setAttribute('height', '18');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('stroke-width', '2');
                svg.innerHTML = '<path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>';
                const name = document.createElement('span');
                name.className = 'text-[13px] font-medium truncate max-w-[180px]';
                name.textContent = msg.attachment.name;
                inner.appendChild(svg);
                inner.appendChild(name);
            }
            link.appendChild(inner);
            bubble.appendChild(link);
        }

        if (msg.message) {
            const p = document.createElement('p');
            p.className = 'text-[13px] leading-relaxed whitespace-pre-wrap';
            p.textContent = msg.message;
            bubble.appendChild(p);
        }

        const time = document.createElement('p');
        time.className = 'text-[10px] text-indigo-200 mt-1 text-right';
        time.textContent = msg.created_at;
        bubble.appendChild(time);

        wrapper.appendChild(bubble);
        this.thread.appendChild(wrapper);
    },

    scrollToBottom() {
        if (this.thread) {
            this.thread.scrollTop = this.thread.scrollHeight;
        }
    },
}));

Alpine.data('adminMessagePoller', () => ({
    lastId: 0,
    timer: null,

    init() {
        this.lastId = Number(this.$el.dataset.lastId || 0);
        const url = this.$el.dataset.pollUrl;
        if (!url) return;

        this.timer = setInterval(() => {
            fetch(`${url}?after=${this.lastId}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.messages?.length) return;
                    d.messages.forEach(m => this.append(m));
                    if (d.last_id) this.lastId = d.last_id;
                })
                .catch(() => {});
        }, 15000);
    },

    append(msg) {
        const thread = document.getElementById('messageThread');
        if (!thread) return;
        document.getElementById('messageEmptyState')?.remove();

        const isOutgoing = msg.direction === 'outgoing';

        const wrapper = document.createElement('div');
        wrapper.className = isOutgoing ? 'flex justify-end' : 'flex justify-start';

        const bubble = document.createElement('div');
        bubble.className = isOutgoing
            ? 'max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-br-md px-3.5 py-2'
            : 'max-w-[85%] bg-white border border-gray-200 rounded-2xl rounded-bl-md px-3.5 py-2 shadow-sm';

        if (msg.message) {
            const p = document.createElement('p');
            p.className = isOutgoing
                ? 'text-[13px] leading-relaxed whitespace-pre-wrap'
                : 'text-[13px] leading-relaxed text-gray-700 whitespace-pre-wrap';
            p.textContent = msg.message;
            bubble.appendChild(p);
        }

        if (msg.attachment) {
            const link = document.createElement('a');
            link.href = msg.attachment.download_url;
            link.target = '_blank';
            link.className = 'block mb-1.5';
            const inner = document.createElement('div');
            inner.className = isOutgoing
                ? 'flex items-center gap-2.5 bg-indigo-500/40 rounded-lg px-3 py-2'
                : 'flex items-center gap-2.5 bg-gray-100 rounded-lg px-3 py-2';
            if (msg.attachment.mime && msg.attachment.mime.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = msg.attachment.url;
                img.alt = msg.attachment.name;
                img.className = 'max-w-[200px] max-h-[160px] rounded-lg object-contain';
                inner.appendChild(img);
            } else {
                const name = document.createElement('span');
                name.className = 'text-[13px] font-medium truncate max-w-[180px]';
                name.textContent = msg.attachment.name;
                inner.appendChild(name);
            }
            link.appendChild(inner);
            bubble.appendChild(link);
        }

        const time = document.createElement('p');
        time.className = isOutgoing ? 'text-[10px] text-indigo-200 mt-1 text-right' : 'text-[10px] text-gray-400 mt-1';
        time.textContent = msg.created_at;
        bubble.appendChild(time);

        wrapper.appendChild(bubble);
        thread.appendChild(wrapper);
        thread.scrollTop = thread.scrollHeight;
    },
}));

Alpine.data('liveChat', (config = {}) => ({
    open: false,
    step: 'idle',
    name: '',
    phone: '',
    formError: '',
    customerName: '',
    messages: [],
    input: '',
    file: null,
    sending: false,
    busy: false,
    lastId: null,
    timer: null,
    faqs: Array.isArray(config.faqs) && config.faqs.length
        ? config.faqs.map((f) => ({ q: f.q, a: f.a }))
        : [
            { q: 'How long does delivery take?', a: 'Delivery usually takes 2–4 business days inside Dhaka and 3–6 business days outside Dhaka.' },
            { q: 'How can I pay?', a: 'We accept Cash on Delivery (COD), bKash, Nagad, Rocket, and card payments.' },
            { q: 'Can I return or exchange a product?', a: 'Yes, you can return or exchange within 3 days of delivery if the product is unused and in original condition.' },
            { q: 'How do I track my order?', a: 'Use the "Track Order" page with your order number and phone number.' },
            { q: 'Can I cancel my order?', a: 'Orders can be cancelled before they are shipped. Message us and we will help you.' },
        ],

    get csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    },

    init() {
        fetch('/live-chat/state', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                if (d.active) {
                    this.customerName = d.name;
                    this.step = 'faq';
                    this.startPolling();
                }
            })
            .catch(() => {});
    },

    openPanel() {
        this.open = true;
        if (this.step === 'idle') {
            this.step = 'form';
            this.$nextTick(() => this.pushBot(`Hi! 👋 Welcome to ${config.storeName || 'our store'}. Please enter your name and phone number to start chatting.`));
        }
    },

    pushBot(text) {
        this.messages.push({ from: 'bot', text });
        this.scrollDown();
    },

    pushUser(text, attachment = null) {
        this.messages.push({ from: 'user', text, attachment });
        this.scrollDown();
    },

    scrollDown() {
        this.$nextTick(() => {
            const el = this.$refs.chatBody;
            if (el) el.scrollTop = el.scrollHeight;
        });
    },

    start() {
        if (this.sending || this.busy) return;
        this.formError = '';

        const name = this.name.trim();
        const phone = this.phone.replace(/[^\d+]/g, '');

        if (!name) {
            this.formError = 'Please enter your name.';
            return;
        }

        if (phone.replace(/\D/g, '').length < 10) {
            this.formError = 'Please enter a valid phone number, e.g. +8801XXXXXXXXX.';
            return;
        }

        this.name = name;
        this.phone = phone;
        this.startChat();
    },

    pickFile(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            this.file = input.files[0];
        }
        input.value = '';
    },

    submit() {
        if (this.sending || this.busy) return;
        const value = this.input.trim();
        if (!value && !this.file) return;

        this.sendMessage(value, this.file);
    },

    async startChat() {
        this.busy = true;
        try {
            const resp = await fetch('/live-chat/start', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                body: JSON.stringify({ name: this.name, phone: this.phone }),
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) {
                this.pushBot(data.error || 'Something went wrong. Please try again.');
                return;
            }
            this.customerName = data.name;
            this.step = 'faq';
            this.pushBot(`Thanks, ${data.name}! You're all set. How can I help you today?`);
        } catch (e) {
            this.pushBot('Network error. Please try again.');
        } finally {
            this.busy = false;
        }
    },

    pickFaq(faq) {
        this.pushUser(faq.q);
        this.pushBot(faq.a);
        fetch('/live-chat/faq', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body: JSON.stringify({ question: faq.q, answer: faq.a }),
        }).catch(() => {});
    },

    connect() {
        if (this.busy) return;
        this.busy = true;
        fetch('/live-chat/connect', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body: JSON.stringify({}),
        })
            .then(r => r.json())
            .then(d => {
                if (d.error) { this.pushBot(d.error); return; }
                this.step = 'connected';
                this.pushBot("I've connected you with a customer agent. Type your question below — we'll reply shortly.");
                this.startPolling();
            })
            .catch(() => this.pushBot('Network error. Please try again.'))
            .finally(() => { this.busy = false; });
    },

    sendMessage(value, file = null) {
        this.sending = true;

        if (file) {
            this.pushUser(value, {
                name: file.name,
                mime: file.type,
                url: URL.createObjectURL(file),
                download_url: URL.createObjectURL(file),
            });
        } else {
            this.pushUser(value);
        }

        this.input = '';
        this.file = null;

        const body = new FormData();
        body.append('message', value);
        if (file) body.append('attachment', file);

        fetch('/live-chat/message', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body,
        })
            .then(r => r.json())
            .then(d => {
                if (d.error) this.pushBot(d.error);
            })
            .catch(() => this.pushBot('Network error. Please try again.'))
            .finally(() => { this.sending = false; });
    },

    startPolling() {
        if (this.timer) return;
        this.timer = setInterval(() => {
            fetch(`/live-chat/messages?after=${this.lastId || 0}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.messages?.length) return;
                    d.messages.forEach(m => {
                        if (m.direction === 'outgoing') {
                            this.messages.push({ from: 'bot', text: m.message, time: m.created_at, attachment: m.attachment || null });
                        }
                    });
                    if (d.last_id) this.lastId = d.last_id;
                    this.scrollDown();
                })
                .catch(() => {});
        }, 5000);
    },
}));

Alpine.data('topBarBuilder', (initial = []) => {
    const defaults = (index) => ({
        id: 'tb_' + Date.now() + '_' + index,
        sort: index,
        name: '',
        status: true,
        type: 'announcement',
        message: '',
        highlight_text: '',
        button_text: '',
        button_url: '',
        icon: '',
        coupon_code: '',
        bg_color: '#4f46e5',
        text_color: '#ffffff',
        highlight_color: '#fbbf24',
        button_color: '#10b981',
        font_size: 'sm',
        font_weight: 'semibold',
        bar_height: '12',
        text_align: 'center',
        icon_position: 'left',
        show_close: true,
        sticky: false,
        animation: 'static',
        countdown_enabled: false,
        countdown_end: '',
        countdown_start: '',
        expired_message: '',
        show_on: ['all'],
        devices: ['all'],
        start: '',
        end: '',
        _open: true,
    });

    const types = window.topBarTypes || {};
    const animations = window.topBarAnimations || {};
    const typesList = Object.entries(types).map(([value, label]) => ({ value, label }));
    const animationsList = Object.entries(animations).map(([value, label]) => ({ value, label }));
    const bars = Array.isArray(initial) ? initial.map((b, i) => Object.assign(defaults(i), b, { _open: false })) : [];

    return {
        types,
        animations,
        typesList,
        animationsList,
        bars,
        serialized: JSON.stringify(bars),

        init() {
            const form = this.$root.closest('form');
            if (form) {
                form.addEventListener('submit', () => this.serialize(), { capture: true });
            }
            this.serialize();
        },
        typeLabel(key) {
            return this.types[key] || key || 'Custom';
        },
        pageLabel(opt) {
            const map = { all: 'All Pages', homepage: 'Homepage', shop: 'Shop', products: 'Product Pages', cart: 'Cart', checkout: 'Checkout' };
            return map[opt] || opt;
        },
        deviceLabel(opt) {
            const map = { all: 'All Devices', desktop: 'Desktop', mobile: 'Mobile', tablet: 'Tablet' };
            return map[opt] || opt;
        },
        addBar() {
            this.bars.push(defaults(this.bars.length));
            this.serialize();
        },
        remove(index) {
            this.bars.splice(index, 1);
            this.bars.forEach((b, i) => { b.sort = i; });
            this.serialize();
        },
        duplicate(index) {
            const copy = JSON.parse(JSON.stringify(this.bars[index]));
            copy.id = 'tb_' + Date.now() + '_' + this.bars.length;
            copy.name = (copy.name || 'Top Bar') + ' (copy)';
            copy._open = true;
            this.bars.splice(index + 1, 0, copy);
            this.serialize();
        },
        move(index, dir) {
            const target = index + dir;
            if (target < 0 || target >= this.bars.length) return;
            const tmp = this.bars[index];
            this.bars[index] = this.bars[target];
            this.bars[target] = tmp;
            this.bars.forEach((b, i) => { b.sort = i; });
            this.serialize();
        },
        serialize() {
            this.bars.forEach((b, i) => { b.sort = i; });
            const clean = this.bars.map((b) => ({
                id: b.id,
                sort: b.sort,
                name: b.name,
                status: !!b.status,
                type: b.type,
                message: b.message,
                highlight_text: b.highlight_text,
                button_text: b.button_text,
                button_url: b.button_url,
                icon: b.icon,
                coupon_code: b.coupon_code,
                bg_color: b.bg_color,
                text_color: b.text_color,
                highlight_color: b.highlight_color,
                button_color: b.button_color,
                font_size: b.font_size,
                font_weight: b.font_weight,
                bar_height: b.bar_height,
                text_align: b.text_align,
                icon_position: b.icon_position,
                show_close: !!b.show_close,
                sticky: !!b.sticky,
                animation: b.animation,
                countdown_enabled: !!b.countdown_enabled,
                countdown_end: b.countdown_end,
                countdown_start: b.countdown_start,
                expired_message: b.expired_message,
                show_on: Array.isArray(b.show_on) ? b.show_on : ['all'],
                devices: Array.isArray(b.devices) ? b.devices : ['all'],
                start: b.start,
                end: b.end,
            }));
            this.serialized = JSON.stringify(clean);
        },
    };
});

Alpine.data('topBarCountdown', (endAt = '', startAt = '', expiredMessage = '') => ({
    endAt,
    startAt,
    expiredMessage,
    display: '',
    timer: null,
    start() {
        this.tick();
        this.timer = setInterval(() => this.tick(), 1000);
    },
    tick() {
        if (!this.endAt) { this.display = ''; return; }
        const now = Date.now();
        const end = new Date(this.endAt).getTime();
        const diff = end - now;
        if (diff <= 0) {
            clearInterval(this.timer);
            this.display = this.expiredMessage || 'Ended';
            return;
        }
        let targetDiff = diff;
        let targetLabel = '';
        if (this.startAt) {
            const start = new Date(this.startAt).getTime();
            if (now < start) {
                targetDiff = start - now;
                targetLabel = 'Starts in ';
            }
        }
        const d = Math.floor(targetDiff / 86400000);
        const h = Math.floor((targetDiff % 86400000) / 3600000);
        const m = Math.floor((targetDiff % 3600000) / 60000);
        const s = Math.floor((targetDiff % 60000) / 1000);
        const pad = n => String(n).padStart(2, '0');
        const formatted = `${pad(d)}D:${pad(h)}H:${pad(m)}M:${pad(s)}S`;
        this.display = `${targetLabel}${formatted}`;
    },
}));

Alpine.data('storePopup', (cfg) => ({
    config: cfg || {},
    open: false,
    armed: false,
    cleanup: [],
    init() {
        if (!this.gatePasses()) return;
        this.track('impression');
        this.arm();
    },
    gatePasses() {
        const id = this.config.id;
        if (this.config.hide_after_close && this.getItem('popup_closed_' + id)) return false;
        if (this.config.hide_after_click && this.getItem('popup_clicked_' + id)) return false;

        const f = this.config.frequency;
        if (f === 'once_per_session') {
            if (this.getSession('popup_visited_' + id)) return false;
        } else if (f === 'once_per_day') {
            if (this.getItem('popup_day_' + id) === this.todayKey()) return false;
        } else if (f === 'once_per_week') {
            if (this.getItem('popup_week_' + id) === this.weekKey()) return false;
        } else if (f === 'once_only') {
            if (this.getItem('popup_seen_' + id)) return false;
        }
        return true;
    },
    markSeen() {
        const id = this.config.id;
        const f = this.config.frequency;
        if (f === 'once_per_session') {
            this.setSession('popup_visited_' + id, '1');
        } else if (f === 'once_per_day') {
            this.setItem('popup_day_' + id, this.todayKey());
        } else if (f === 'once_per_week') {
            this.setItem('popup_week_' + id, this.weekKey());
        } else if (f === 'once_only') {
            this.setItem('popup_seen_' + id, '1');
        }
    },
    arm() {
        const trigger = this.config.trigger;
        if (trigger === 'immediately') {
            this.show();
        } else if (trigger === 'after_seconds') {
            const t = setTimeout(() => this.show(), (Number(this.config.trigger_seconds) || 0) * 1000);
            this.cleanup.push(() => clearTimeout(t));
        } else if (trigger === 'on_scroll') {
            this.armScroll();
        } else if (trigger === 'exit_intent') {
            this.armExitIntent();
        } else if (trigger === 'after_add_to_cart') {
            const fn = () => this.show();
            window.addEventListener('cart-updated', fn);
            this.cleanup.push(() => window.removeEventListener('cart-updated', fn));
        }
    },
    armScroll() {
        const target = (Number(this.config.scroll_percent) || 50) / 100;
        let fired = false;
        const fn = () => {
            if (fired || this.open) return;
            const doc = document.documentElement;
            const scrolled = (window.scrollY || doc.scrollTop) /
                ((doc.scrollHeight - doc.clientHeight) || 1);
            if (scrolled >= target) {
                fired = true;
                this.show();
            }
        };
        window.addEventListener('scroll', fn, { passive: true });
        this.cleanup.push(() => window.removeEventListener('scroll', fn));
        fn();
    },
    armExitIntent() {
        let fired = false;
        const fn = (e) => {
            if (fired || this.open) return;
            if (e.clientY <= 0) {
                fired = true;
                this.show();
            }
        };
        document.addEventListener('mouseleave', fn);
        this.cleanup.push(() => document.removeEventListener('mouseleave', fn));
    },
    show() {
        if (this.open || this.armed) return;
        this.armed = true;
        this.open = true;
        this.markSeen();
        this.track('view');
    },
    cta(event) {
        this.track('click');
        if (this.config.hide_after_click) {
            this.setItem('popup_clicked_' + this.config.id, '1');
        }
        if (!this.config.has_button_url) {
            if (event && event.preventDefault) event.preventDefault();
            this.open = false;
        }
    },
    close() {
        this.open = false;
        this.track('close');
        if (this.config.hide_after_close) {
            this.setItem('popup_closed_' + this.config.id, '1');
        }
    },
    track(type) {
        const body = new FormData();
        body.append('popup_id', this.config.id);
        body.append('type', type);
        body.append('page', window.location.pathname);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            fetch('/popup/track', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                body,
                credentials: 'same-origin',
            });
        } catch (e) { /* ignore tracking errors */ }
    },
    todayKey() {
        const d = new Date();
        return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    },
    weekKey() {
        const d = new Date();
        const onejan = new Date(d.getFullYear(), 0, 1);
        const week = Math.ceil(((d - onejan) / 86400000 + onejan.getDay() + 1) / 7);
        return d.getFullYear() + '-W' + week;
    },
    getItem(key) {
        try { return localStorage.getItem(key); } catch (e) { return null; }
    },
    setItem(key, value) {
        try { localStorage.setItem(key, value); } catch (e) {}
    },
    getSession(key) {
        try { return sessionStorage.getItem(key); } catch (e) { return null; }
    },
    setSession(key, value) {
        try { sessionStorage.setItem(key, value); } catch (e) {}
    },
}));

Alpine.data('fakeOrders', (opts) => ({
    visible: false,
    message: '',
    timer: null,
    template: (opts && opts.template) || '{name} from {city} just purchased {product}.',
    names: ['Farhan', 'Nusrat', 'Tanvir', 'Sumaiya', 'Rakib', 'Ayesha', 'Imran', 'Tasnim', 'Mim', 'Shakib'],
    cities: ['Dhaka', 'Chattogram', 'Khulna', 'Rajshahi', 'Sylhet', 'Barishal', 'Rangpur', 'Mymensingh', 'Gazipur', 'Narayanganj'],
    products: [],
    index: 0,
    start() {
        this.products = Array.from(document.querySelectorAll('[data-product-name]'))
            .map(el => el.textContent.trim())
            .filter(Boolean);
        if (!this.products.length) {
            this.products = ['Elegant Denim Jacket', 'Premium Wireless Earbuds', 'Cotton Oversized Tee', 'Leather Handbag', 'Running Shoes', 'Smart Watch'];
        }
        const interval = Number(document.getElementById('fake-order-interval')?.value || 20) * 1000;
        const show = () => {
            if (this.visible) { setTimeout(show, interval); return; }
            const city = this.cities[Math.floor(Math.random() * this.cities.length)];
            const name = this.names[Math.floor(Math.random() * this.names.length)];
            const product = this.products[this.index++ % this.products.length];
            this.message = this.template
                .replace(/\{name\}/g, name)
                .replace(/\{city\}/g, city)
                .replace(/\{product\}/g, product);
            this.visible = true;
            setTimeout(() => { this.visible = false; }, 6000);
            this.timer = setTimeout(show, interval);
        };
        this.timer = setTimeout(show, interval);
    },
}));

Alpine.data('flashCountdown', (endIso) => ({
    end: new Date(endIso).getTime(),
    now: Date.now(),
    timer: null,
    get done() {
        return this.now >= this.end;
    },
    get d() { return Math.floor((this.end - this.now) / 86400000); },
    get h() { return Math.floor(((this.end - this.now) % 86400000) / 3600000); },
    get m() { return Math.floor(((this.end - this.now) % 3600000) / 60000); },
    get s() { return Math.floor(((this.end - this.now) % 60000) / 1000); },
    get padded() {
        const unit = (v, two = false) => String(Math.max(0, v)).padStart(two ? 2 : 1, '0');
        return { d: unit(this.d), h: unit(this.h, true), m: unit(this.m, true), s: unit(this.s, true) };
    },
    init() {
        this.timer = setInterval(() => { this.now = Date.now(); }, 1000);
    },
    destroy() {
        clearInterval(this.timer);
    },
}));

Alpine.data('flashCarousel', (totalItems, minPerView) => ({
    totalItems,
    minPerView,
    page: 0,
    perView: 0,
    gap: 0,
    recalcTimer: null,
    autoplayTimer: null,
    autoplayEvery: 4000,
    autoPaused: false,
    hovered: false,

    get total() {
        return this.totalItems;
    },

    get pages() {
        if (this.perView <= 0) return 1;
        return Math.max(1, Math.ceil(this.total / this.perView));
    },

    get pageCount() {
        return this.pages;
    },

    get canPrev() {
        return this.page > 0;
    },

    get canNext() {
        return this.page < this.pages - 1;
    },

    startAutoplay() {
        this.stopAutoplay();
        if (this.pages <= 1) return;
        this.autoplayTimer = setInterval(() => {
            if (this.autoPaused || this.hovered) return;
            if (this.canNext) {
                this.advance();
            } else {
                this.rewind();
            }
        }, this.autoplayEvery);
    },

    stopAutoplay() {
        if (this.autoplayTimer) {
            clearInterval(this.autoplayTimer);
            this.autoplayTimer = null;
        }
    },

    hover() {
        this.hovered = true;
    },

    leave() {
        this.hovered = false;
    },

    cardStep() {
        const step = this.perView || this.minPerView;
        return Math.max(1, step);
    },

    measure() {
        const track = this.$refs.track;
        if (!track) return;

        const trackWidth = track.clientWidth;
        const firstCard = track.firstElementChild;
        const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 0;

        if (trackWidth <= 0 || cardWidth <= 0) {
            this.perView = this.minPerView;
        } else {
            const style = window.getComputedStyle(track);
            this.gap = parseFloat(style.columnGap) || parseFloat(style.gap) || 0;
            this.perView = Math.max(1, Math.round((trackWidth + this.gap) / (cardWidth + this.gap)));
        }

        if (this.total <= this.perView) {
            this.page = 0;
        } else {
            this.page = Math.min(this.page, this.pages - 1);
        }
        this.render();
    },

    scrollToPage(idx, animated = true) {
        const track = this.$refs.track;
        if (!track) return;

        const card = track.children[Math.min(idx * this.cardStep(), track.children.length - 1)];
        if (!card) return;

        const target = card.offsetLeft - track.offsetLeft;
        let max = track.scrollWidth - track.clientWidth;
        if (max < 0) max = 0;
        const clamped = Math.max(0, Math.min(target, max));

        track.scrollTo({
            left: clamped,
            behavior: animated && this.autoPaused === false && !this.hovered ? 'smooth' : 'auto',
        });

        this.page = idx;
    },

    advance() {
        if (!this.canNext) return;
        this.scrollToPage(this.page + 1);
    },

    rewind() {
        this.scrollToPage(0);
    },

    next() {
        this.advance();
        this.autoPaused = true;
        this.stopAutoplay();
    },

    prev() {
        if (!this.canPrev) return;
        this.scrollToPage(this.page - 1);
        this.autoPaused = true;
        this.stopAutoplay();
    },

    go(idx) {
        idx = Math.max(0, Math.min(this.pages - 1, idx));
        this.scrollToPage(idx);
        this.autoPaused = true;
        this.stopAutoplay();
    },

    onScroll() {
        if (!this.autoPaused) return;
        const track = this.$refs.track;
        if (!track) return;
        const firstCard = track.firstElementChild;
        if (!firstCard) return;
        const step = (firstCard.getBoundingClientRect().width + this.gap) * this.cardStep();
        if (step > 0) {
            this.page = Math.max(0, Math.min(this.pages - 1, Math.round(track.scrollLeft / step)));
        }
    },

    render() {
        const track = this.$refs.track;
        if (!track) return;
        const card = track.children[Math.min(this.page * this.cardStep(), track.children.length - 1)];
        if (!card) return;
        let max = track.scrollWidth - track.clientWidth;
        if (max < 0) max = 0;
        const target = Math.max(0, Math.min(card.offsetLeft - track.offsetLeft, max));
        if (track.scrollLeft !== target) {
            track.scrollLeft = target;
        }
    },

    init() {
        this.recalcTimer = setInterval(() => this.measure(), 400);
        window.addEventListener('resize', () => this.measure());
        this.measure();
        this.startAutoplay();
    },

    destroy() {
        this.stopAutoplay();
        if (this.recalcTimer) clearInterval(this.recalcTimer);
        window.removeEventListener('resize', () => this.measure());
    },
}));

Alpine.start();
