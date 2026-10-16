@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('popupBuilder', (initial) => {
            return {
                config: initial || {},
                form: { image_url: '' },
                selectedPagesText: Array.isArray(initial && initial.selected_pages)
                    ? initial.selected_pages.join('\n')
                    : '',
                register() {
                    const self = this;
                    this.$watch('selectedPagesText', (value) => {
                        self.config.selected_pages = value.split('\n')
                            .map(p => p.trim())
                            .filter(p => p !== '');
                    });
                },
                toggle(field, value) {
                    const arr = this.config[field] || [];
                    const i = arr.indexOf(value);
                    if (i > -1) {
                        this.config[field] = arr.filter(x => x !== value);
                    } else {
                        this.config[field] = [...arr, value];
                    }
                },
                serialize() {
                    if (this.selectedPagesText.trim() !== '') {
                        this.config.selected_pages = this.selectedPagesText.split('\n')
                            .map(p => p.trim())
                            .filter(p => p !== '');
                    }
                    document.getElementById('popupConfigJson').value = JSON.stringify(this.config);
                },
            };
        });
    });

    function previewPopupImage(input) {
        const preview = document.getElementById('popupImagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                const builder = Alpine.$data(document.querySelector('[x-data*="popupBuilder"]'));
                if (builder) {
                    builder.form.image_url = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
