<?php
/** Je presente les produits de la requete WooCommerce dans ma grille par categorie. */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
do_action( 'woocommerce_before_main_content' );

$groups = array();
$counts = sae301_catalogue_counts();
$catalogue_items = array();
if ( is_front_page() && ! is_shop() ) {
    // Je reutilise mon catalogue sur l'accueil sans modifier les reglages de lecture.
    $catalogue_items = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'menu_order ID', 'order' => 'ASC' ) );
} else {
    while ( have_posts() ) {
        the_post();
        $catalogue_items[] = wc_get_product( get_the_ID() );
    }
}
foreach ( $catalogue_items as $catalogue_product ) {
    if ( ! $catalogue_product || ! $catalogue_product->is_visible() ) {
        continue;
    }
    $terms = get_the_terms( $catalogue_product->get_id(), 'product_cat' );
    $term  = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
    if ( is_product_category() ) {
        $term = get_queried_object();
    }
    $group_id = $term ? $term->term_id : 0;
    if ( ! isset( $groups[ $group_id ] ) ) {
        $groups[ $group_id ] = array( 'name' => $term ? $term->name : 'Produits', 'items' => array() );
    }
    $groups[ $group_id ]['items'][] = $catalogue_product;
}
// Je classe les rayons comme sur ma maquette sans figer les identifiants des categories.
$ranks = array( 'boissons' => 0, 'boisson' => 0, 'plats' => 1, 'plat' => 1, 'desserts' => 2, 'dessert' => 2 );
uasort( $groups, static function ( $left, $right ) use ( $ranks ) {
    return ( $ranks[ sanitize_title( $left['name'] ) ] ?? 10 ) <=> ( $ranks[ sanitize_title( $right['name'] ) ] ?? 10 );
} );
?>
<div class="sae-catalogue">
    <div class="sae-catalogue__intro">
        <h1 class="sae-catalogue__title"><?php echo is_front_page() || is_shop() ? 'Nos Plats à Emporter' : esc_html( woocommerce_page_title( false ) ); ?></h1>
        <?php sae301_brand_cart(); ?>
    </div>
    <?php woocommerce_output_all_notices(); ?>
    <p class="sae-catalogue__feedback" role="status" aria-live="polite" aria-atomic="true"></p>
    <?php if ( ! $groups ) : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
    <?php endif; ?>
    <?php foreach ( $groups as $group_id => $group ) : ?>
        <section class="sae-catalogue__section" aria-labelledby="sae-category-<?php echo esc_attr( $group_id ); ?>">
            <h2 class="sae-catalogue__category" id="sae-category-<?php echo esc_attr( $group_id ); ?>"><?php echo esc_html( 'Nos ' . $group['name'] ); ?></h2>
            <ul class="sae-catalogue__grid" role="list" tabindex="0" aria-label="<?php echo esc_attr( 'Produits : ' . $group['name'] . '. Sur petit écran, faire défiler horizontalement pour voir les autres produits.' ); ?>">
                <?php foreach ( $group['items'] as $catalogue_product ) :
                    $id       = $catalogue_product->get_id();
                    $name     = $catalogue_product->get_name();
                    $url      = $catalogue_product->get_permalink();
                    $quantity = $counts[ (string) $id ] ?? 0;
                    $is_dish  = in_array( sanitize_title( $group['name'] ), array( 'plat', 'plats' ), true );
                    $can_add  = $catalogue_product->is_type( 'simple' ) && $catalogue_product->is_purchasable() && $catalogue_product->is_in_stock();
                    ?>
                    <li class="sae-card<?php echo $is_dish ? ' sae-card--dish' : ''; ?>" data-product-id="<?php echo esc_attr( $id ); ?>">
                        <a class="sae-card__image-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
                            <?php echo $catalogue_product->get_image( 'full', array( 'class' => 'sae-card__image' ) ); // Je garde l'image originale, sans recadrage carre impose par WooCommerce. ?>
                        </a>
                        <div class="sae-card__content">
                            <div class="sae-card__text">
                                <div class="sae-card__heading">
                                    <h3 class="sae-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
                                    <div class="sae-card__price"><?php echo wp_kses_post( $catalogue_product->get_price_html() ); ?></div>
                                </div>
                                <p class="sae-card__description"><?php echo esc_html( wp_strip_all_tags( $catalogue_product->get_short_description() ?: $catalogue_product->get_description() ) ); ?></p>
                            </div>
                            <?php if ( $catalogue_product->is_type( 'simple' ) ) : ?>
                                <div class="sae-quantity" role="group" aria-label="<?php echo esc_attr( 'Quantité de ' . $name . ' dans le panier' ); ?>" data-purchasable="<?php echo $can_add ? '1' : '0'; ?>" data-max="<?php echo esc_attr( $catalogue_product->get_max_purchase_quantity() ); ?>">
                                    <button class="sae-quantity__button" type="button" data-delta="-1" aria-label="<?php echo esc_attr( 'Retirer une unité de ' . $name ); ?>" disabled><span aria-hidden="true">−</span></button>
                                    <output class="sae-quantity__value" aria-label="Quantité dans le panier"><?php echo esc_html( $quantity ); ?></output>
                                    <button class="sae-quantity__button" type="button" data-delta="1" aria-label="<?php echo esc_attr( 'Ajouter une unité de ' . $name ); ?>" disabled><span aria-hidden="true">+</span></button>
                                </div>
                                <?php if ( ! $can_add ) : ?><p class="sae-card__availability">Indisponible</p><?php endif; ?>
                                <noscript><a href="<?php echo esc_url( $url ); ?>">Choisir la quantité sur la fiche produit</a></noscript>
                            <?php else : ?>
                                <a class="button sae-card__options" href="<?php echo esc_url( $url ); ?>">Choisir les options</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>
    <?php woocommerce_pagination(); ?>
</div>
<?php
wp_reset_postdata();
do_action( 'woocommerce_after_main_content' );
get_footer( 'shop' );
