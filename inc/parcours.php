<?php
// checkout
defined( 'ABSPATH' ) || exit;

function ajouter_classes_parcours( $classes ) {
    if ( is_cart() || ( is_checkout() && ! is_order_received_page() ) ) {
        $classes[] = 'parcours';
        $classes[] = is_cart() ? 'parcours--cart' : 'parcours--checkout';
    }
    return $classes;
}
add_filter( 'body_class', 'ajouter_classes_parcours', 50 );

function preparer_pages_commande() {
    if ( ! is_cart() && ( ! is_checkout() || is_order_received_page() ) ) {
        return;
    }
    remove_action( 'storefront_page', 'storefront_page_header', 10 );
    remove_action( 'storefront_page', 'storefront_edit_post_link', 30 );
    add_action( 'storefront_page', 'afficher_entete_commande', 10 );

}
add_action( 'wp', 'preparer_pages_commande', 40 );

function afficher_entete_commande() {
    ?>
    <header class="parcours__header">
        <nav class="parcours__breadcrumb" aria-label="Fil d’Ariane">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
            <span aria-hidden="true">›</span>
            <?php if ( is_checkout() ) : ?>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">Panier</a>
                <span aria-hidden="true">›</span>
            <?php endif; ?>
            <span aria-current="page"><?php echo is_cart() ? 'Panier' : 'Commande'; ?></span>
        </nav>
        <div class="parcours__intro">
            <h1 class="parcours__title"><?php echo is_cart() ? 'Votre panier' : 'Votre commande'; ?></h1>
            <?php if ( is_cart() ) { afficher_lien_panier(); } ?>
        </div>
    </header>
    <?php
}

function preparer_images_panier( $images ) {
    foreach ( $images as $image ) {
        $source = wp_get_attachment_image_url( $image->id, 'full' );
        if ( $source ) {
            $image->thumbnail = $source;
            $image->src = $source;
            $image->srcset = '';
            $image->thumbnail_srcset = '';
            $image->thumbnail_sizes = '20.9rem';
        }
    }
    return $images;
}
add_filter( 'woocommerce_store_api_cart_item_images', 'preparer_images_panier' );

function charger_script_panier() {
    if ( ! is_cart() ) {
        return;
    }
    wp_enqueue_script(
        'restaurant-panier',
        get_stylesheet_directory_uri() . '/assets/js/panier.js',
        array( 'wc-blocks-checkout', 'wc-blocks-data-store', 'wp-element', 'wp-data', 'wp-html-entities', 'wp-notices' ),
        (string) filemtime( get_stylesheet_directory() . '/assets/js/panier.js' ),
        true
    );
    wp_localize_script( 'restaurant-panier', 'donneesPanier', array( 'shopUrl' => wc_get_page_permalink( 'shop' ) ) );
}
add_action( 'wp_enqueue_scripts', 'charger_script_panier', 60 );

function afficher_code_promo_panier( $contenu ) {
    if ( is_cart() ) {
        return '<div data-block-name="restaurant/cart-coupon" class="code_promo"></div>' . $contenu;
    }
    return $contenu;
}
add_filter( 'render_block_woocommerce/cart-totals-block', 'afficher_code_promo_panier' );

function retirer_code_promo_natif( $contenu ) {
    return is_cart() ? '' : $contenu;
}
add_filter( 'render_block_woocommerce/cart-order-summary-coupon-form-block', 'retirer_code_promo_natif' );

function afficher_livraison_panier( $contenu ) {
    if ( ! is_cart() ) {
        return $contenu;
    }
    $html = new WP_HTML_Tag_Processor( $contenu );
    while ( $html->next_tag( 'DIV' ) ) {
        if ( 'woocommerce/cart-order-summary-shipping-block' === $html->get_attribute( 'data-block-name' ) ) {
            $html->set_attribute( 'data-block-name', 'restaurant/cart-shipping' );
            $html->set_attribute( 'class', 'livraison' );
            break;
        }
    }
    return $html->get_updated_html();
}
add_filter( 'render_block_woocommerce/cart-order-summary-block', 'afficher_livraison_panier' );


function preparer_resume_commande( $bloc ) {
    if ( ! is_checkout() || is_order_received_page() || 'woocommerce/checkout-totals-block' !== $bloc['blockName'] ) {
        return $bloc;
    }

    $bloc['innerContent'][0] .= '<h2 class="parcours__resume_titre">Votre commande</h2>';
    $actions = parse_blocks( '<!-- wp:woocommerce/checkout-actions-block --><div class="wp-block-woocommerce-checkout-actions-block"></div><!-- /wp:woocommerce/checkout-actions-block -->' );
    $bloc['innerBlocks'][] = $actions[0];


    array_splice( $bloc['innerContent'], -1, 0, array( null ) );
    return $bloc;
}
add_filter( 'render_block_data', 'preparer_resume_commande' );


function retirer_actions_formulaire( $contenu, $bloc, $parent ) {
    if ( is_checkout() && ! is_order_received_page() && $parent
        && 'woocommerce/checkout-actions-block' === $bloc['blockName']
        && 'woocommerce/checkout-fields-block' === $parent->name ) {
        return '';
    }
    return $contenu;
}
add_filter( 'pre_render_block', 'retirer_actions_formulaire', 10, 3 );

function traduire_blocs_commande() {
    if ( ! is_checkout() || is_order_received_page() ) {
        return;
    }
    wp_add_inline_script(
        'wc-checkout-block-frontend',
        'wp.i18n.setLocaleData({"Contact information":["Vos coordonnées"],"Payment options":["Mode de paiement"]}, "woocommerce");',
        'before'
    );
}

add_action( 'wp_print_footer_scripts', 'traduire_blocs_commande', 5 );
function ajouter_classe_confirmation( $classes ) {
    if ( is_order_received_page() ) {
        $classes[] = 'receipt_page';
    }
    return $classes;
}
add_filter( 'body_class', 'ajouter_classe_confirmation', 50 );

function preparer_confirmation() {
    if ( ! is_order_received_page() ) {
        return;
    }
    remove_action( 'storefront_page', 'storefront_page_header', 10 );
    remove_action( 'storefront_page', 'storefront_edit_post_link', 30 );
}
add_action( 'wp', 'preparer_confirmation', 40 );

function autoriser_annulation_commande( $statuts ) {
    $statuts[] = 'on-hold';
    $statuts[] = 'processing';

    return $statuts;
}
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'autoriser_annulation_commande' );
