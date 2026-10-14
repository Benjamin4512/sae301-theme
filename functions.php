<?php
/**
 * Fonctions du theme enfant Sae301.
 * Storefront charge deja ses styles et le style.css du theme enfant.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sae301_setup() {
    load_child_theme_textdomain( 'sae301', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'sae301_setup' );
