<?php
/** Je relie mon catalogue et mes cartes aux produits WooCommerce. */
defined( 'ABSPATH' ) || exit;

function sae301_catalogue_page() {
    return function_exists( 'is_shop' ) && ( is_front_page() || is_shop() || is_product_taxonomy() );
}

function sae301_catalogue_layout() {
    // Je retire la colonne de widgets sans supprimer ses contenus en administration.
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'sae301_catalogue_layout', 20 );

function sae301_catalogue_classes( $classes ) {
    $classes = array_diff( $classes, array( 'right-sidebar', 'left-sidebar' ) );
    $classes[] = 'sae301--full-width';
    if ( sae301_catalogue_page() ) {
        $classes[] = 'sae301--catalogue';
    }
    return $classes;
}
add_filter( 'body_class', 'sae301_catalogue_classes', 30 );

function sae301_catalogue_assets() {
    if ( ! sae301_catalogue_page() ) {
        return;
    }
    wp_enqueue_script( 'sae301-catalogue', get_stylesheet_directory_uri() . '/assets/js/catalogue.js', array( 'jquery', 'wc-cart-fragments' ), (string) filemtime( __DIR__ . '/../assets/js/catalogue.js' ), true );
    wp_localize_script( 'sae301-catalogue', 'sae301Catalogue', array(
        'endpoint' => WC_AJAX::get_endpoint( 'sae301_quantity' ),
        'nonce'    => wp_create_nonce( 'sae301_quantity' ),
        'error'    => 'Je ne peux pas mettre à jour le panier. Merci de réessayer.',
    ) );
}
add_action( 'wp_enqueue_scripts', 'sae301_catalogue_assets', 40 );

function sae301_catalogue_template( $template ) {
    if ( sae301_catalogue_page() ) {
        return __DIR__ . '/../templates/catalogue.php';
    }
    return $template;
}
add_filter( 'template_include', 'sae301_catalogue_template', 99 );

function sae301_catalogue_order( $args ) {
    // Je garde le tri manuel WooCommerce, puis l'ordre de creation en cas d'egalite.
    if ( sae301_catalogue_page() && ! isset( $_GET['orderby'] ) && 'menu_order' === get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) ) {
        $args['orderby'] = 'menu_order ID';
        $args['order']   = 'ASC';
    }
    return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'sae301_catalogue_order' );

function sae301_catalogue_counts() {
    $counts = array();
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $item ) {
            $id = (string) $item['product_id'];
            $counts[ $id ] = ( $counts[ $id ] ?? 0 ) + $item['quantity'];
        }
    }
    return $counts;
}

function sae301_catalogue_quantity() {
    if ( ! check_ajax_referer( 'sae301_quantity', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'La session a expiré. Merci de recharger la page.' ), 403 );
    }
    $id    = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    $delta = isset( $_POST['delta'] ) ? (int) $_POST['delta'] : 0;
    $item_product = wc_get_product( $id );
    if ( ! in_array( $delta, array( -1, 1 ), true ) || ! $item_product || ! $item_product->is_type( 'simple' ) || 'publish' !== $item_product->get_status() ) {
        wp_send_json_error( array( 'message' => 'Ce produit ne peut pas être modifié ici.' ), 400 );
    }
    if ( ! WC()->cart ) {
        wc_load_cart();
    }
    $key  = WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $id ) );
    $item = $key ? WC()->cart->get_cart_item( $key ) : null;
    try {
        if ( 1 === $delta ) {
            if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $id, 1 ) || ! WC()->cart->add_to_cart( $id, 1 ) ) {
                throw new Exception( 'Ajout impossible : merci de vérifier le stock ou les options du produit.' );
            }
        } elseif ( $item ) {
            $quantity = max( 0, $item['quantity'] - 1 );
            if ( ! apply_filters( 'woocommerce_update_cart_validation', true, $key, $item, $quantity ) || ! WC()->cart->set_quantity( $key, $quantity ) ) {
                throw new Exception( 'La quantité ne peut pas être modifiée.' );
            }
        }
        WC()->cart->calculate_totals();
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        $fragments = apply_filters( 'woocommerce_add_to_cart_fragments', array( 'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>' ) );
        wp_send_json_success( array( 'counts' => (object) sae301_catalogue_counts(), 'fragments' => $fragments, 'cart_hash' => WC()->cart->get_cart_hash() ) );
    } catch ( Exception $error ) {
        wp_send_json_error( array( 'message' => wp_strip_all_tags( $error->getMessage() ) ), 400 );
    }
}
add_action( 'wc_ajax_sae301_quantity', 'sae301_catalogue_quantity' );

// Je partage l'en-tete et le pied de page sur toutes mes pages.
require_once __DIR__ . '/apparence.php';
