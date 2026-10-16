/* Je synchronise les boutons de mes cartes avec le vrai panier WooCommerce. */
(() => {
    'use strict';
    const catalogue = document.querySelector('.sae-catalogue');
    if (!catalogue || typeof sae301Catalogue === 'undefined') return;
    const controls = [...catalogue.querySelectorAll('.sae-quantity')];
    const feedback = catalogue.querySelector('.sae-catalogue__feedback');
    let busy = false;

    function refreshControls(counts) {
        for (const control of controls) {
            const card = control.closest('[data-product-id]');
            const output = control.querySelector('output');
            if (counts) output.textContent = String(counts[card.dataset.productId] || 0);
            const current = Number(output.textContent);
            const maximum = Number(control.dataset.max);
            control.querySelector('[data-delta="-1"]').disabled = busy || current <= 0;
            control.querySelector('[data-delta="1"]').disabled = busy || control.dataset.purchasable !== '1' || (maximum >= 0 && current >= maximum);
        }
    }
    refreshControls();

    catalogue.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-delta]');
        if (!button || button.disabled || busy) return;
        const card = button.closest('[data-product-id]');
        busy = true;
        card.setAttribute('aria-busy', 'true');
        card.querySelector('.sae-quantity').classList.add('sae-quantity--loading');
        refreshControls();
        feedback.textContent = '';
        feedback.classList.remove('sae-catalogue__feedback--visible');
        try {
            const response = await fetch(sae301Catalogue.endpoint, {
                method: 'POST', credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
                body: new URLSearchParams({nonce: sae301Catalogue.nonce, product_id: card.dataset.productId, delta: button.dataset.delta})
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.data?.message || sae301Catalogue.error);
            refreshControls(result.data.counts);
            if (window.jQuery) {
                const $ = window.jQuery;
                for (const [selector, html] of Object.entries(result.data.fragments)) $(selector).replaceWith(html);
                $(document.body).trigger('wc_fragments_refreshed');
                $(document.body).trigger('wc_fragment_refresh');
            }
            const count = result.data.counts[card.dataset.productId] || 0;
            feedback.textContent = `${card.querySelector('.sae-card__title').textContent.trim()} : ${count} dans le panier.`;
            feedback.classList.add('screen-reader-text');
        } catch (error) {
            feedback.classList.remove('screen-reader-text');
            feedback.classList.add('sae-catalogue__feedback--visible');
            feedback.textContent = error.message || sae301Catalogue.error;
        } finally {
            busy = false;
            card.removeAttribute('aria-busy');
            card.querySelector('.sae-quantity').classList.remove('sae-quantity--loading');
            refreshControls();
        }
    });
    window.addEventListener('pageshow', (event) => { if (event.persisted) window.location.reload(); });
})();
