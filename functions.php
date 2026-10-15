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

function sae301_enqueue_design_system() {
    // Je remplace la police du parent par celles de mon Design System.
    wp_dequeue_style( 'storefront-fonts' );
    wp_enqueue_style(
        'sae301-fonts',
        'https://fonts.googleapis.com/css2?family=Metamorphous&family=Noto+Sans:wght@400;500&display=swap',
        array(),
        null
    );

    $stylesheet = '/assets/css/design-system.css';
    wp_enqueue_style(
        'sae301-design-system',
        get_stylesheet_directory_uri() . $stylesheet,
        array( 'storefront-style', 'sae301-fonts' ),
        (string) filemtime( get_stylesheet_directory() . $stylesheet )
    );
    // Je laisse Storefront charger mon style.css a sa priorite habituelle (30).
}
add_action( 'wp_enqueue_scripts', 'sae301_enqueue_design_system', 20 );

// Je charge les cartes et la grille de mon catalogue.
require_once get_stylesheet_directory() . '/inc/catalogue.php';
