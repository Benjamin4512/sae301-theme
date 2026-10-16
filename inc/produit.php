<?php

defined( 'ABSPATH' ) || exit;

function sae301_product_layout() {
    if ( ! is_product() ) {
        return;
    }
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    remove_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'storefront_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    remove_action( 'woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 30 );
    add_action( 'woocommerce_single_product_summary', 'sae301_product_category', 4 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 8 );
    add_action( 'woocommerce_single_product_summary', 'sae301_product_information', 9 );
    add_action( 'woocommerce_before_add_to_cart_form', 'sae301_product_options_intro' );
    add_action( 'woocommerce_before_quantity_input_field', 'sae301_product_quantity_start' );
    add_action( 'woocommerce_after_quantity_input_field', 'sae301_product_quantity_end' );
}
add_action( 'wp', 'sae301_product_layout', 30 );

function sae301_product_body( $classes ) {
    if ( is_product() ) {
        $classes[] = 'sae301--product';
    }
    return $classes;
}
add_filter( 'body_class', 'sae301_product_body', 40 );

function sae301_product_category() {
    global $product;
    $categories = get_the_terms( $product->get_id(), 'product_cat' );
    if ( empty( $categories ) || is_wp_error( $categories ) ) {
        return;
    }
    $category = reset( $categories );
    echo '<p class="sae-product__category">Nos ' . esc_html( $category->name ) . '</p>';
}

function sae301_product_options_intro() {
    global $product;
    if ( ! $product->is_type( 'variable' ) ) {
        return;
    }
    $labels = array();
    foreach ( array_keys( $product->get_variation_attributes() ) as $attribute ) {
        $labels[] = mb_strtolower( wc_attribute_label( $attribute, $product ) );
    }
    ?>
    <div class="sae-product__options-intro">
        <h2 class="sae-product__options-title">Personnalisez votre <?php echo has_term( 'boissons', 'product_cat', $product->get_id() ) ? 'boisson' : ( has_term( 'dessert', 'product_cat', $product->get_id() ) || has_term( 'desserts', 'product_cat', $product->get_id() ) ? 'dessert' : 'plat' ); ?></h2>
        <p class="sae-product__options-help">Choisissez vos options : <?php echo esc_html( implode( ', ', $labels ) ); ?>.</p>
    </div>
    <?php
}

function sae301_product_quantity_start() {
    ?>
    <span class="sae-product__quantity-label">Quantité</span>
    <div class="sae-product__quantity-control">
        <button class="sae-product__quantity-button" type="button" data-product-step="-1" aria-label="Diminuer la quantité">−</button>
    <?php
}

function sae301_product_quantity_end() {
    ?>
        <button class="sae-product__quantity-button" type="button" data-product-step="1" aria-label="Augmenter la quantité">+</button>
    </div>
    <?php
}

function sae301_product_select_class( $args ) {
    if ( is_product() ) {
        $args['class'] = trim( ( $args['class'] ?? '' ) . ' sae-product__select' );
    }
    return $args;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'sae301_product_select_class' );

function sae301_product_assets() {
    if ( is_product() ) {
        wp_enqueue_script( 'sae301-produit', get_stylesheet_directory_uri() . '/assets/js/produit.js', array( 'jquery', 'wc-add-to-cart-variation' ), (string) filemtime( __DIR__ . '/../assets/js/produit.js' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'sae301_product_assets', 45 );

function sae301_product_information() {
    if ( ! function_exists( 'get_field' ) ) {
        return;
    }
    $id = get_the_ID();
    $composition = get_field( 'composition', $id );
    $nutrition = get_field( 'valeurs_nutrionelles', $id );
    $allergenes = get_field( 'allergenes', $id );
    if ( is_array( $allergenes ) ) {
        $allergenes = implode( ' • ', $allergenes );
    }
    if ( ! $composition && ! $nutrition && ! $allergenes ) {
        return;
    }
    ?>
    <div class="sae-product__details">
        <div class="sae-product-info">
            <?php if ( $composition ) : ?>
                <section class="sae-product-info__group">
                    <h2 class="sae-product-info__title">Composition</h2>
                    <p class="sae-product-info__text"><?php echo nl2br( esc_html( $composition ) ); ?></p>
                </section>
            <?php endif; ?>
            <?php if ( $nutrition ) : ?>
                <section class="sae-product-info__group">
                    <h2 class="sae-product-info__title">Valeurs nutritionnelles</h2>
                    <p class="sae-product-info__text"><?php echo nl2br( esc_html( str_replace( '|', "\n", $nutrition ) ) ); ?></p>
                </section>
            <?php endif; ?>
            <?php if ( $allergenes ) : ?>
                <section class="sae-product-info__group sae-product-info__group--last">
                    <h2 class="sae-product-info__title">Allergènes</h2>
                    <p class="sae-product-info__text"><?php echo esc_html( $allergenes ); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
