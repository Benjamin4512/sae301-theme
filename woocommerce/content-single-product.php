<?php
defined( 'ABSPATH' ) || exit;
global $product;
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
$shop_url = wc_get_page_permalink( 'shop' );
?>
<div class="produit_entete">
    <nav class="produit_entete__breadcrumb" aria-label="Fil d’Ariane">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a><span aria-hidden="true">›</span>
        <a href="<?php echo esc_url( $shop_url ); ?>">Nos Plats à Emporter</a><span aria-hidden="true">›</span>
        <span aria-current="page"><?php echo esc_html( $product->get_name() ); ?></span>
    </nav>
    <?php afficher_lien_panier(); ?>
</div>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'produit', $product ); ?>>
    <div class="produit__layout">
    <div class="produit__media<?php echo has_term( 'boissons', 'product_cat', $product->get_id() ) ? ' produit__media--drink' : ''; ?>">
        <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
    </div>
    <div class="summary entry-summary produit__summary">
        <?php do_action( 'woocommerce_single_product_summary' ); ?>
    </div>
    <a class="produit__back" href="<?php echo esc_url( $shop_url ); ?>">← Continuer mes achats</a>
    </div>
    <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
