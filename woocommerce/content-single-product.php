<?php
/** Je presente les options de mes produits avec le formulaire WooCommerce. */
defined( 'ABSPATH' ) || exit;
global $product;
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
$shop_url = wc_get_page_permalink( 'shop' );
?>
<div class="sae_produit_entete">
    <nav class="sae_produit_entete__breadcrumb" aria-label="Fil d’Ariane">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a><span aria-hidden="true">›</span>
        <a href="<?php echo esc_url( $shop_url ); ?>">Nos Plats à Emporter</a><span aria-hidden="true">›</span>
        <span aria-current="page"><?php echo esc_html( $product->get_name() ); ?></span>
    </nav>
    <?php sae301_brand_cart(); ?>
</div>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'sae_produit', $product ); ?>>
    <div class="sae_produit__layout">
    <div class="sae_produit__media<?php echo has_term( 'boissons', 'product_cat', $product->get_id() ) ? ' sae_produit__media--drink' : ''; ?>">
        <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
    </div>
    <div class="summary entry-summary sae_produit__summary">
        <?php do_action( 'woocommerce_single_product_summary' ); ?>
    </div>
    <a class="sae_produit__back" href="<?php echo esc_url( $shop_url ); ?>">← Continuer mes achats</a>
    </div>
    <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
