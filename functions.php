<?php
/**
 * Je configure le theme enfant Sae301.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function configurer_theme() {
    load_child_theme_textdomain( 'sae301', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'configurer_theme' );

function ajouter_classes_site( $classes ) {
    $classes[] = 'site_restaurant';
    if ( is_front_page() ) {
        $classes[] = 'site_restaurant--accueil';
    }
    return $classes;
}
add_filter( 'body_class', 'ajouter_classes_site' );

function charger_styles_theme() {
    wp_dequeue_style( 'storefront-fonts' );
    // Je charge mon unique feuille de style apres Storefront.
    wp_dequeue_style( 'storefront-child-style' );
    wp_deregister_style( 'storefront-child-style' );
    wp_enqueue_style(
        'restaurant-style',
        get_stylesheet_uri(),
        array( 'storefront-style' ),
        (string) filemtime( get_stylesheet_directory() . '/style.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'charger_styles_theme', 50 );

function classer_styles_externes( $html, $handle, $href, $media ) {
    if ( is_admin() || in_array( $handle, array( 'restaurant-style', 'admin-bar', 'dashicons' ), true ) ) {
        return $html;
    }
    return '<style id="' . esc_attr( $handle ) . '-css">@import url("' . esc_url_raw( $href ) . '") layer(reset) ' . esc_html( $media ) . ';</style>';
}
add_filter( 'style_loader_tag', 'classer_styles_externes', 20, 4 );

function classer_styles_integres( $handles ) {
    if ( is_admin() ) {
        return $handles;
    }
    $styles = wp_styles();
    foreach ( $handles as $handle ) {
        if ( in_array( $handle, array( 'restaurant-style', 'admin-bar', 'dashicons' ), true ) ) {
            continue;
        }
        $inline = $styles->get_data( $handle, 'after' );
        if ( $inline && ! $styles->get_data( $handle, 'restaurant-layered' ) ) {
            foreach ( $inline as &$css ) {
                if ( false === strpos( $css, 'wp_block_styles_on_demand_placeholder:' ) ) {
                    $css = '@layer reset {' . $css . '}';
                }
            }
            unset( $css );
            $styles->add_data( $handle, 'after', $inline );
            $styles->add_data( $handle, 'restaurant-layered', true );
        }
    }
    return $handles;
}
add_filter( 'print_styles_array', 'classer_styles_integres', 20 );


// Chaque fichier regroupe les fonctions d'une partie du site.
require_once __DIR__ . '/inc/apparence.php';
require_once __DIR__ . '/inc/catalogue.php';
require_once __DIR__ . '/inc/produit.php';
require_once __DIR__ . '/inc/tarification.php';
require_once __DIR__ . '/inc/parcours.php';
require_once __DIR__ . '/inc/equipe.php';
