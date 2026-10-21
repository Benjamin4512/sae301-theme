<?php
/** Frais de préparation configurables depuis la page d’options Tarification de SCF. */
defined( 'ABSPATH' ) || exit;

function sae301_frais_preparation( $cart ) {
    if ( ( is_admin() && ! wp_doing_ajax() ) || $cart->is_empty() || ! function_exists( 'get_field' ) ) {
        return;
    }

    $montant = (float) wc_format_decimal( get_field( 'montant', 'option' ) );
    $seuil   = (float) wc_format_decimal( get_field( 'seuil', 'option' ) );
    if ( $montant <= 0 || $seuil <= 0 ) {
        return;
    }

    // Montant TTC des produits après remise, hors livraison et hors frais.
    $total_produits = round( $cart->get_cart_contents_total() + $cart->get_cart_contents_tax(), wc_get_price_decimals() );
    if ( $total_produits <= $seuil ) {
        $cart->add_fee( 'Frais de préparation', $montant, false );
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'sae301_frais_preparation', 20 );
