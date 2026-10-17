<?php
/**
 * Je configure le theme enfant Sae301.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sae301_setup() {
    load_child_theme_textdomain( 'sae301', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'sae301_setup' );

function sae301_body_classes( $classes ) {
    $classes[] = 'sae301';
    return $classes;
}
add_filter( 'body_class', 'sae301_body_classes' );

function sae301_enqueue_styles() {
    wp_dequeue_style( 'storefront-fonts' );
    wp_enqueue_style(
        'sae301-fonts',
        'https://fonts.googleapis.com/css2?family=Metamorphous&family=Noto+Sans:wght@400;500&display=swap',
        array(),
        null
    );
    // Je charge mon unique feuille de style apres Storefront.
    wp_dequeue_style( 'storefront-child-style' );
    wp_deregister_style( 'storefront-child-style' );
    wp_enqueue_style(
        'sae301-style',
        get_stylesheet_uri(),
        array( 'storefront-style', 'sae301-fonts' ),
        (string) filemtime( get_stylesheet_directory() . '/style.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'sae301_enqueue_styles', 50 );

// Je charge les cartes et la grille de mon catalogue.
require_once get_stylesheet_directory() . '/inc/catalogue.php';

// Je charge la presentation de mes fiches produit.
require_once get_stylesheet_directory() . '/inc/produit.php';

function sae301_order_classes( $classes ) {
    if ( is_cart() || ( is_checkout() && ! is_order_received_page() ) ) {
        $classes[] = 'sae-flow';
        $classes[] = is_cart() ? 'sae-flow--cart' : 'sae-flow--checkout';
    }
    return $classes;
}
add_filter( 'body_class', 'sae301_order_classes', 50 );

function sae301_order_layout() {
    if ( ! is_cart() && ( ! is_checkout() || is_order_received_page() ) ) {
        return;
    }
    remove_action( 'storefront_page', 'storefront_page_header', 10 );
    remove_action( 'storefront_page', 'storefront_edit_post_link', 30 );
    add_action( 'storefront_page', 'sae301_order_header', 10 );

}
add_action( 'wp', 'sae301_order_layout', 40 );

function sae301_order_header() {
    ?>
    <header class="sae-flow__header">
        <nav class="sae-flow__breadcrumb" aria-label="Fil d’Ariane">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
            <span aria-hidden="true">›</span>
            <?php if ( is_checkout() ) : ?>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">Panier</a>
                <span aria-hidden="true">›</span>
            <?php endif; ?>
            <span aria-current="page"><?php echo is_cart() ? 'Panier' : 'Commande'; ?></span>
        </nav>
        <div class="sae-flow__intro">
            <h1 class="sae-flow__title"><?php echo is_cart() ? 'Votre panier' : 'Votre commande'; ?></h1>
            <?php if ( is_cart() ) { sae301_brand_cart(); } ?>
        </div>
    </header>
    <?php
}

function sae301_order_back() {
    if ( is_cart() ) {
        echo '<a class="sae-flow__back" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">← Continuer mes achats</a>';
    }
}

function sae301_order_images( $images ) {
    foreach ( $images as $image ) {
        $source = wp_get_attachment_image_url( $image->id, 'full' );
        if ( $source ) {
            $image->thumbnail = $source;
            $image->src = $source;
            $image->srcset = '';
            $image->thumbnail_srcset = '';
            $image->thumbnail_sizes = '20.9375rem';
        }
    }
    return $images;
}
add_filter( 'woocommerce_store_api_cart_item_images', 'sae301_order_images' );

function sae301_cart_controls() {
    if ( ! is_cart() ) {
        return;
    }
    wp_enqueue_script(
        'sae301-panier',
        get_stylesheet_directory_uri() . '/assets/js/panier.js',
        array( 'wc-blocks-checkout', 'wc-blocks-data-store', 'wp-element', 'wp-data', 'wp-html-entities', 'wp-notices' ),
        (string) filemtime( get_stylesheet_directory() . '/assets/js/panier.js' ),
        true
    );
    wp_localize_script( 'sae301-panier', 'sae301Cart', array( 'shopUrl' => wc_get_page_permalink( 'shop' ) ) );
}
add_action( 'wp_enqueue_scripts', 'sae301_cart_controls', 60 );

function sae301_cart_blocks( $content ) {
    if ( ! is_cart() ) {
        return $content;
    }
    $document = new DOMDocument();
    $previous = libxml_use_internal_errors( true );
    $document->loadHTML( '<?xml encoding="utf-8" ?>' . $content );
    libxml_clear_errors();
    libxml_use_internal_errors( $previous );
    $xpath = new DOMXPath( $document );
    $filled = $xpath->query( '//*[@data-block-name="woocommerce/filled-cart-block"]' )->item( 0 );
    $totals = $xpath->query( '//*[@data-block-name="woocommerce/cart-totals-block"]' )->item( 0 );
    $coupon = $xpath->query( '//*[@data-block-name="woocommerce/cart-order-summary-coupon-form-block"]' )->item( 0 );
    $shipping = $xpath->query( '//*[@data-block-name="woocommerce/cart-order-summary-shipping-block"]' )->item( 0 );
    if ( $filled && $totals && $coupon ) {
        $coupon->setAttribute( 'data-block-name', 'sae301/cart-coupon' );
        $coupon->setAttribute( 'class', 'sae-cart-coupon' );
        $filled->insertBefore( $coupon, $totals );
    }
    if ( $shipping ) {
        $shipping->setAttribute( 'data-block-name', 'sae301/cart-shipping' );
        $shipping->setAttribute( 'class', 'sae-cart-shipping' );
    }
    $result = '';
    foreach ( $document->getElementsByTagName( 'body' )->item( 0 )->childNodes as $node ) {
        $result .= $document->saveHTML( $node );
    }
    return $result;
}
add_filter( 'render_block_woocommerce/cart', 'sae301_cart_blocks' );

function sae301_checkout_layout( $content ) {
    if ( ! is_checkout() || is_order_received_page() ) {
        return $content;
    }
    sae301_checkout_texts();
    $document = new DOMDocument();
    $previous = libxml_use_internal_errors( true );
    $document->loadHTML( '<?xml encoding="utf-8" ?>' . $content );
    libxml_clear_errors();
    libxml_use_internal_errors( $previous );
    $xpath = new DOMXPath( $document );
    $totals = $xpath->query( '//*[@data-block-name="woocommerce/checkout-totals-block"]' )->item( 0 );
    $actions = $xpath->query( '//*[@data-block-name="woocommerce/checkout-actions-block"]' )->item( 0 );
    if ( $totals && $actions ) {
        $title = $document->createElement( 'h2', 'Votre commande' );
        $title->setAttribute( 'class', 'sae-checkout__summary-title' );
        $totals->insertBefore( $title, $totals->firstChild );
        $totals->appendChild( $actions );
    }
    $result = '';
    foreach ( $document->getElementsByTagName( 'body' )->item( 0 )->childNodes as $node ) {
        $result .= $document->saveHTML( $node );
    }
    return $result;
}
add_filter( 'render_block_woocommerce/checkout', 'sae301_checkout_layout' );


function sae301_checkout_texts() {
    if ( ! is_checkout() || is_order_received_page() ) {
        return;
    }
    wp_add_inline_script(
        'wc-checkout-block-frontend',
        'wp.i18n.setLocaleData({"Contact information":["Vos coordonnées"],"Payment options":["Mode de paiement"]}, "woocommerce");',
        'before'
    );
}
add_action( 'wp_enqueue_scripts', 'sae301_checkout_texts', 100 );
function sae301_receipt_classes( $classes ) {
    if ( is_order_received_page() ) {
        $classes[] = 'sae-receipt-page';
    }
    return $classes;
}
add_filter( 'body_class', 'sae301_receipt_classes', 50 );

function sae301_receipt_layout() {
    if ( ! is_order_received_page() ) {
        return;
    }
    remove_action( 'storefront_page', 'storefront_page_header', 10 );
    remove_action( 'storefront_page', 'storefront_edit_post_link', 30 );
}
add_action( 'wp', 'sae301_receipt_layout', 40 );
