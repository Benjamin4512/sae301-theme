<?php
/** Présentation des contenus du restaurant. */
defined( 'ABSPATH' ) || exit;

function est_page_catalogue() {
    return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_tax( 'gamme' ) );
}

function preparer_catalogue() {
    // Je retire la colonne de widgets sans supprimer ses contenus en administration.
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'preparer_catalogue', 20 );

function ajouter_classes_catalogue( $classes ) {
    $classes = array_diff( $classes, array( 'right-sidebar', 'left-sidebar' ) );
    $classes[] = 'site_restaurant--full_width';
    if ( est_page_catalogue() ) {
        $classes[] = 'site_restaurant--catalogue';
    }
    return $classes;
}
add_filter( 'body_class', 'ajouter_classes_catalogue', 30 );

function charger_script_catalogue() {
    if ( ! est_page_catalogue() ) {
        return;
    }
    wp_enqueue_script( 'restaurant-catalogue', get_stylesheet_directory_uri() . '/assets/js/catalogue.js', array( 'jquery', 'wc-cart-fragments' ), (string) filemtime( __DIR__ . '/../assets/js/catalogue.js' ), true );
    wp_localize_script( 'restaurant-catalogue', 'donneesCatalogue', array(
        'endpoint' => WC_AJAX::get_endpoint( 'modifier_quantite_catalogue' ),
        'nonce'    => wp_create_nonce( 'modifier_quantite_catalogue' ),
        'error'    => 'Je ne peux pas mettre à jour le panier. Merci de réessayer.',
    ) );
}
add_action( 'wp_enqueue_scripts', 'charger_script_catalogue', 40 );

function choisir_template_catalogue( $template ) {
    if ( is_tax( 'gamme' ) ) {
        return get_stylesheet_directory() . '/taxonomy-gamme.php';
    }
    if ( est_page_catalogue() ) {
        return __DIR__ . '/../templates/catalogue.php';
    }
    return $template;
}
add_filter( 'template_include', 'choisir_template_catalogue', 99 );

function trier_produits_catalogue( $args ) {
    // Je garde le tri manuel WooCommerce, puis l'ordre de creation en cas d'egalite.
    if ( est_page_catalogue() && ! isset( $_GET['orderby'] ) && 'menu_order' === get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) ) {
        $args['orderby'] = 'menu_order ID';
        $args['order']   = 'ASC';
    }
    return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'trier_produits_catalogue' );

function compter_produits_panier() {
    $counts = array();
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $item ) {
            $id = (string) $item['product_id'];
            $counts[ $id ] = ( $counts[ $id ] ?? 0 ) + $item['quantity'];
        }
    }
    return $counts;
}

function modifier_quantite_catalogue() {
    if ( ! check_ajax_referer( 'modifier_quantite_catalogue', 'nonce', false ) ) {
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
        wp_send_json_success( array( 'counts' => (object) compter_produits_panier(), 'fragments' => $fragments, 'cart_hash' => WC()->cart->get_cart_hash() ) );
    } catch ( Exception $error ) {
        wp_send_json_error( array( 'message' => wp_strip_all_tags( $error->getMessage() ) ), 400 );
    }
}
add_action( 'wc_ajax_modifier_quantite_catalogue', 'modifier_quantite_catalogue' );

// Je partage l'en-tete et le pied de page sur toutes mes pages.

