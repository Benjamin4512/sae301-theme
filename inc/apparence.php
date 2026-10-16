<?php
/** Je partage l'identite visuelle du restaurant sur toutes les pages de mon theme. */
defined( 'ABSPATH' ) || exit;

function sae301_brand_layout() {
    $header_hooks = array(
        0  => array( 'storefront_header_container' ),
        10 => array( 'storefront_social_icons' ),
        20 => array( 'storefront_site_branding' ),
        30 => array( 'storefront_secondary_navigation' ),
        40 => array( 'storefront_product_search' ),
        41 => array( 'storefront_header_container_close' ),
        42 => array( 'storefront_primary_navigation_wrapper' ),
        50 => array( 'storefront_primary_navigation' ),
        60 => array( 'storefront_header_cart' ),
        68 => array( 'storefront_primary_navigation_wrapper_close' ),
    );
    foreach ( $header_hooks as $priority => $callbacks ) {
        foreach ( $callbacks as $callback ) {
            remove_action( 'storefront_header', $callback, $priority );
        }
    }
    // Je conserve les liens d'evitement et les points d'entree du theme parent.
    remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
    remove_action( 'storefront_before_content', 'storefront_header_widget_region', 10 );
    remove_action( 'storefront_footer', 'storefront_footer_widgets', 10 );
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
    remove_action( 'storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999 );
    add_action( 'storefront_header', 'sae301_brand_header', 20 );
    add_action( 'storefront_footer', 'sae301_brand_footer', 20 );
}
add_action( 'wp', 'sae301_brand_layout', 25 );


function sae301_brand_header() {
    require __DIR__ . '/../templates/brand-header.php';
}

function sae301_brand_footer() {
    require __DIR__ . '/../templates/brand-footer.php';
}

function sae301_brand_cart() {
    if ( ! function_exists( 'WC' ) ) {
        return;
    }
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ?>
    <a class="sae-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( 'Mon panier : ' . $count . ' article(s)' ); ?>">
        <span>Mon Panier</span>
        <span class="sae-cart-link__badge"><span class="sae-cart-link__count"><?php echo esc_html( $count ); ?></span><img class="sae-cart-link__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/icons/bag.svg' ); ?>" width="18" height="24" alt=""></span>
    </a>
    <?php
}

function sae301_brand_cart_fragment( $fragments ) {
    ob_start();
    sae301_brand_cart();
    $fragments['a.sae-cart-link'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sae301_brand_cart_fragment' );
