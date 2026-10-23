<?php
defined( 'ABSPATH' ) || exit;

function preparer_interface_commune() {
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
  
    remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
    remove_action( 'storefront_before_content', 'storefront_header_widget_region', 10 );
    remove_action( 'storefront_footer', 'storefront_footer_widgets', 10 );
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
    remove_action( 'storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999 );
    add_action( 'storefront_header', 'afficher_header', 20 );
    add_action( 'storefront_footer', 'afficher_footer', 20 );
}
add_action( 'wp', 'preparer_interface_commune', 25 );


function afficher_header() {
    require __DIR__ . '/../templates/brand-header.php';
}

function afficher_footer() {
    require __DIR__ . '/../templates/brand-footer.php';
}

function afficher_lien_panier() {
    if ( ! function_exists( 'WC' ) ) {
        return;
    }
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ?>
    <a class="panier_lien" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( 'Mon panier : ' . $count . ' article(s)' ); ?>">
        <span>Mon Panier</span>
        <span class="panier_lien__badge"><span class="panier_lien__count"><?php echo esc_html( $count ); ?></span><img class="panier_lien__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/icons/bag.svg' ); ?>" width="18" height="24" alt=""></span>
    </a>
    <?php
}

function actualiser_lien_panier( $fragments ) {
    ob_start();
    afficher_lien_panier();
    $fragments['a.panier_lien'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'actualiser_lien_panier' );
