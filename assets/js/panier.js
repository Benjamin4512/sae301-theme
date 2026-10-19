(function () {
    const element = wp.element.createElement;
    const store = 'wc/store/cart';
    const decode = wp.htmlEntities.decodeEntities;

    function Coupon() {
        const [code, setCode] = wp.element.useState('');
        const [message, setMessage] = wp.element.useState('');
        const [hasError, setHasError] = wp.element.useState(false);
        const cart = wp.data.useSelect(function (select) {
            return {
                busy: select(store).isApplyingCoupon(),
                coupons: select(store).getCartData().coupons || []
            };
        }, []);
        const appliedCodes = cart.coupons.map(function (coupon) { return coupon.code; }).join(', ');

        wp.element.useEffect(function () {
            setHasError(false);
            setMessage(appliedCodes ? 'Code promo activé : ' + appliedCodes + '.' : '');
        }, [appliedCodes]);

        function apply(event) {
            event.preventDefault();
            const coupon = code.trim();
            if (!coupon || cart.busy) {
                return;
            }
            setMessage('');
            setHasError(false);
            wp.data.dispatch(store).applyCoupon(coupon).then(function () {
                setMessage('Le code promo « ' + coupon + ' » est activé.');
                setCode('');
            }).catch(function (error) {
                setHasError(true);
                setMessage(decode(error.message || 'Impossible d’appliquer ce code promo. Veuillez réessayer.'));
            });
        }

        return element('section', {className: 'sae-cart-coupon'},
            element('label', {className: 'sae-cart-coupon__label', htmlFor: 'sae-coupon'}, 'Vous avez un code promo ?'),
            element('form', {className: 'sae-cart-coupon__form', onSubmit: apply},
                element('input', {
                    className: 'sae-cart-coupon__input', id: 'sae-coupon',
                    type: 'text', value: code, placeholder: 'Code promo', disabled: cart.busy,
                    'aria-describedby': 'sae-coupon-message', 'aria-invalid': hasError,
                    onChange: function (event) { setCode(event.target.value); }
                }),
                element('button', {
                    className: 'sae-cart-coupon__button', type: 'submit',
                    disabled: cart.busy || !code.trim()
                }, cart.busy ? 'Application…' : 'Appliquer le code promo')
            ),
            element('p', {
                className: 'sae-cart-coupon__message' + (hasError ? ' sae-cart-coupon__message--error' : ''),
                id: 'sae-coupon-message', role: hasError ? 'alert' : 'status',
                'aria-live': 'polite', 'aria-atomic': true, hidden: !message
            }, message),
            element('a', {className: 'sae-flow__back', href: sae301Cart.shopUrl}, '← Continuer mes achats')
        );
    }

    function Shipping() {
        const data = wp.data.useSelect(function (select) {
            return {
                packages: select(store).getShippingRates(),
                busy: select(store).isShippingRateBeingSelected(),
                needsShipping: select(store).getNeedsShipping(),
                totals: select(store).getCartTotals()
            };
        }, []);
        if (!data.needsShipping || !data.totals.currency_code) {
            return null;
        }

        const subtotal = (Number(data.totals.total_items) + Number(data.totals.total_items_tax)) / Math.pow(10, data.totals.currency_minor_unit);
        return element('section', {className: 'sae-cart-shipping'},
            element('div', {className: 'sae-cart-shipping__subtotal'},
                element('span', null, 'Sous-total'),
                element('span', null, subtotal.toLocaleString('fr-FR', {style: 'currency', currency: data.totals.currency_code}))
            ),
            element('h3', {className: 'sae-cart-shipping__title'}, 'Mode d’expédition'),
            data.packages.length ? data.packages.map(function (pack) {
                return element('fieldset', {className: 'sae-cart-shipping__choices', key: pack.package_id},
                    element('legend', {className: 'screen-reader-text'}, 'Choisir le mode d’expédition'),
                    pack.shipping_rates.map(function (rate) {
                        const price = Number(rate.price) / Math.pow(10, rate.currency_minor_unit);
                        const label = decode(rate.name) + ' — ' + (price === 0 ? 'gratuit' : price.toLocaleString('fr-FR', {
                            style: 'currency', currency: rate.currency_code
                        }));
                        return element('label', {className: 'sae-cart-shipping__option', key: rate.rate_id},
                            element('input', {
                                className: 'sae-cart-shipping__radio', type: 'radio',
                                name: 'sae-shipping-' + pack.package_id, value: rate.rate_id,
                                checked: rate.selected, disabled: data.busy,
                                onChange: function () {
                                    wp.data.dispatch(store).selectShippingRate(rate.rate_id, pack.package_id).catch(function (error) {
                                        wp.data.dispatch('core/notices').createErrorNotice(error.message, {context: 'wc/cart'});
                                    });
                                }
                            }),
                            element('span', {className: 'sae-cart-shipping__text'}, label)
                        );
                    })
                );
            }) : element('p', null, 'Les modes de livraison seront proposés après la saisie de votre adresse à la commande.')
        );
    }

    wc.blocksCheckout.registerCheckoutBlock({
        metadata: {name: 'sae301/cart-coupon', parent: ['woocommerce/filled-cart-block']},
        component: Coupon
    });
    wc.blocksCheckout.registerCheckoutBlock({
        metadata: {name: 'sae301/cart-shipping', parent: ['woocommerce/cart-order-summary-totals-block', 'woocommerce/cart-order-summary-block']},
        component: Shipping
    });
})();
