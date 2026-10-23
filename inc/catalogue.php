<?php
// plats du restaurants
defined( 'ABSPATH' ) || exit;

function est_page_catalogue() {
    return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_tax( 'gamme' ) || ( is_search() && 'product' === get_query_var( 'post_type' ) ) );
}

function preparer_catalogue() {
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





function preparer_bloc_filtres( $bloc ) {
    if ( is_admin() || ! est_page_catalogue() ) {
        return $bloc;
    }
    if ( 'woocommerce/product-filters' === $bloc['blockName'] ) {
        $bloc['attrs']['overlayMode'] = 'off';
        foreach ( $bloc['innerBlocks'] as &$enfant ) {
            $enfant = preparer_bloc_filtres( $enfant );
            if ( 'core/heading' === $enfant['blockName'] ) {
                $enfant['innerHTML'] = '';
                $enfant['innerContent'] = array( '' );
            }
        }
        unset( $enfant );
    }
    if ( 'core/search' === $bloc['blockName'] ) {
        $bloc['attrs']['label'] = 'Rechercher par nom';
        $bloc['attrs']['placeholder'] = 'Nom du produit';
        $bloc['attrs']['query']['post_type'] = 'product';
    
        foreach ( $_GET as $nom => $valeur ) {
            if ( is_string( $valeur ) && ( in_array( $nom, array( 'min_price', 'max_price', 'categories' ), true ) || str_starts_with( $nom, 'filter_' ) || str_starts_with( $nom, 'query_type_' ) ) ) {
                $bloc['attrs']['query'][ $nom ] = sanitize_text_field( wp_unslash( $valeur ) );
            }
        }
    }
    return $bloc;
}
add_filter( 'render_block_data', 'preparer_bloc_filtres' );

add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

function conserver_page_recherche( $contenu ) {
    if ( ! est_page_catalogue() ) {
        return $contenu;
    }
    $lien = is_tax() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
    $html = new WP_HTML_Tag_Processor( $contenu );
    if ( ! is_wp_error( $lien ) && $html->next_tag( 'FORM' ) ) {
        $html->set_attribute( 'action', $lien );
    }
    return $html->get_updated_html();
}
add_filter( 'render_block_core/search', 'conserver_page_recherche' );


function preparer_champs_prix( $contenu ) {
    if ( ! est_page_catalogue() ) {
        return $contenu;
    }
    $html = new WP_HTML_Tag_Processor( $contenu );
    while ( $html->next_tag( 'INPUT' ) ) {
        if ( 'text' !== $html->get_attribute( 'type' ) ) {
            continue;
        }
        $minimum = $html->has_class( 'min' );
        $parametre = $minimum ? 'min_price' : 'max_price';
        $valeur = isset( $_GET[ $parametre ] ) && is_string( $_GET[ $parametre ] )
            ? wc_format_localized_price( wc_format_decimal( wp_unslash( $_GET[ $parametre ] ) ) )
            : '';
        $html->set_attribute( 'placeholder', $minimum ? 'Min' : 'Max' );
        $html->set_attribute( 'inputmode', 'decimal' );
        $html->set_attribute( 'value', $valeur );
        $html->remove_attribute( 'data-wp-bind--value' );
    }
    return $html->get_updated_html();
}
add_filter( 'render_block_woocommerce/product-filter-price-slider', 'preparer_champs_prix' );
