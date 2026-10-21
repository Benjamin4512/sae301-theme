<?php

defined( 'ABSPATH' ) || exit;

// Réserver les avis aux clients connectés ayant acheté le produit concerné.
add_filter( 'pre_option_woocommerce_review_rating_verification_required', function () {
    return 'yes';
} );


function sae301_product_layout() {
    if ( ! is_product() ) {
        return;
    }
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    remove_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    add_action( 'woocommerce_after_single_product_summary', 'sae301_product_reviews', 10 );
    add_filter( 'woocommerce_reviews_title', 'sae301_reviews_title' );
    add_filter( 'woocommerce_product_review_comment_form_args', 'sae301_review_form' );
    remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
    remove_action( 'woocommerce_review_meta', 'woocommerce_review_display_meta', 10 );
    add_action( 'woocommerce_review_meta', 'sae301_review_meta', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'storefront_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    add_action( 'woocommerce_after_single_product_summary', 'sae301_product_recommendations', 20 );
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
    echo '<p class="sae_produit__category">Nos ' . esc_html( $category->name ) . '</p>';
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
    <div class="sae_produit__options_intro">
        <h2 class="sae_produit__options_title">Personnalisez votre <?php echo has_term( 'boissons', 'product_cat', $product->get_id() ) ? 'boisson' : ( has_term( 'dessert', 'product_cat', $product->get_id() ) || has_term( 'desserts', 'product_cat', $product->get_id() ) ? 'dessert' : 'plat' ); ?></h2>
        <p class="sae_produit__options_help">Choisissez vos options : <?php echo esc_html( implode( ', ', $labels ) ); ?>.</p>
    </div>
    <?php
}

function sae301_product_quantity_start() {
    ?>
    <span class="sae_produit__quantity_label">Quantité</span>
    <div class="sae_produit__quantity_control">
        <button class="sae_produit__quantity_button sae_bouton" type="button" data-product-step="-1" aria-label="Diminuer la quantité">−</button>
    <?php
}

function sae301_product_quantity_end() {
    ?>
        <button class="sae_produit__quantity_button sae_bouton" type="button" data-product-step="1" aria-label="Augmenter la quantité">+</button>
    </div>
    <?php
}

function sae301_product_select_class( $args ) {
    if ( is_product() ) {
        $args['class'] = trim( ( $args['class'] ?? '' ) . ' sae_produit__select' );
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
    <div class="sae_produit__details">
        <div class="sae_produit_infos">
            <?php if ( $composition ) : ?>
                <section class="sae_produit_infos__group">
                    <h2 class="sae_produit_infos__title">Composition</h2>
                    <p class="sae_produit_infos__text"><?php echo nl2br( esc_html( $composition ) ); ?></p>
                </section>
            <?php endif; ?>
            <?php if ( $nutrition ) : ?>
                <section class="sae_produit_infos__group">
                    <h2 class="sae_produit_infos__title">Valeurs nutritionnelles</h2>
                    <p class="sae_produit_infos__text"><?php echo nl2br( esc_html( str_replace( '|', "\n", $nutrition ) ) ); ?></p>
                </section>
            <?php endif; ?>
            <?php if ( $allergenes ) : ?>
                <section class="sae_produit_infos__group sae_produit_infos__group--last">
                    <h2 class="sae_produit_infos__title">Allergènes</h2>
                    <p class="sae_produit_infos__text"><?php echo esc_html( $allergenes ); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </div>
    <?php
}


function sae301_product_recommendations() {
    global $product;
    $suggestions = array();
    $linked_ids = $product->get_cross_sell_ids();
    foreach ( array( 'boissons', 'plats', 'desserts' ) as $category ) {
        if ( has_term( $category, 'product_cat', $product->get_id() ) ) {
            continue;
        }
        $candidates = wc_get_products( array(
            'status'       => 'publish',
            'limit'        => -1,
            'category'     => array( $category ),
            'stock_status' => 'instock',
            'exclude'      => array( $product->get_id() ),
            'orderby'      => 'ID',
            'order'        => 'ASC',
        ) );
        $available = array();
        foreach ( $candidates as $candidate ) {
            if ( $candidate->is_visible() && $candidate->is_purchasable() ) {
                $available[] = $candidate;
            }
        }
        if ( ! $available ) {
            continue;
        }
        $choice = $available[ $product->get_id() % count( $available ) ];
        foreach ( $available as $candidate ) {
            if ( in_array( $candidate->get_id(), $linked_ids, true ) ) {
                $choice = $candidate;
                break;
            }
        }
        $suggestions[] = $choice;
        if ( count( $suggestions ) === 2 ) {
            break;
        }
    }
    if ( $suggestions ) {
        get_template_part( 'templates/recommandations', null, array( 'products' => $suggestions ) );
    }
}

// Présentation des avis : les formulaires et contrôles restent gérés par WooCommerce.
function sae301_product_reviews() {
    global $product;
    if ( ! comments_open() ) {
        return;
    }
    $count = $product->get_review_count();
    ?>
    <section class="sae_avis" aria-labelledby="sae_reviews_title">
        <header class="sae_avis__header">
            <h2 id="sae_reviews_title">Avis clients</h2>
            <p><?php
                if ( $count && wc_review_ratings_enabled() && $product->get_rating_count() ) {
                    echo esc_html( number_format_i18n( (float) $product->get_average_rating(), 1 ) . '/5 · ' );
                }
                echo esc_html( $count . ' avis' );
            ?></p>
        </header>
        <?php comments_template(); ?>
    </section>
    <?php
}

function sae301_reviews_title( $title ) {
    return 'Les derniers avis';
}

function sae301_review_form( $form ) {
    $form['title_reply'] = 'Donnez votre avis';
    $help = get_option( 'woocommerce_review_rating_verification_required' ) === 'yes'
        ? 'Réservé aux acheteurs certifiés.' : 'Partagez votre expérience avec ce produit.';
    $form['title_reply_after'] = '</span><p class="sae_avis__help">' . esc_html( $help ) . '</p>';
    $form['label_submit'] = 'Publier mon avis';
    $form['comment_field'] = str_replace(
        array( 'Your review', 'Votre avis', '<textarea id="comment"' ),
        array( 'Votre commentaire', 'Votre commentaire', '<textarea placeholder="Partagez votre expérience…" id="comment"' ),
        $form['comment_field']
    );
    return $form;
}

function sae301_review_meta( $comment ) {
    if ( '0' === $comment->comment_approved ) {
        echo '<p class="woocommerce-review__awaiting-approval">' . esc_html__( 'Your review is awaiting approval', 'woocommerce' ) . '</p>';
        return;
    }
    ?>
    <p class="sae_avis_item__meta">
        <span class="sae_avis_item__author"><?php echo esc_html( get_comment_author( $comment ) ); ?></span>
        <?php if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' && wc_review_is_from_verified_owner( $comment->comment_ID ) ) : ?>
            <span class="sae_avis_item__verified">Achat vérifié</span>
        <?php endif; ?>
        <time class="sae_avis_item__date" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'd/m/Y', $comment ) ); ?></time>
    </p>
    <?php
}
