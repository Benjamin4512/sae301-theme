/* Je garde les menus et les variations de WooCommerce. */
jQuery(function ($) {
    function updateSelects() {
        $('.produit__select').each(function () {
            $(this).toggleClass('produit__select--selected', this.value !== '');
        });
    }

    $('.variations_form').on('change woocommerce_update_variation_values reset_data', updateSelects);
    updateSelects();

    $('.produit').on('click', '[data-product-step]', function () {
        var input = this.closest('.quantity').querySelector('input.qty');
        if (!input || input.disabled || input.readOnly || input.type !== 'number') {
            return;
        }
        if (this.dataset.productStep === '1') {
            input.stepUp();
        } else {
            input.stepDown();
        }
        $(input).trigger('change');
    });
});
