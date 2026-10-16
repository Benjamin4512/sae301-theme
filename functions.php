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
