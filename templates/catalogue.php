<?php
/** Présentation des contenus du restaurant. */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
do_action( 'woocommerce_before_main_content' );

$groups = array();
$counts = compter_produits_panier();
while ( have_posts() ) {
    the_post();
    $catalogue_product = wc_get_product( get_the_ID() );
    if ( ! $catalogue_product || ! $catalogue_product->is_visible() ) {
        continue;
    }
    $terms = get_the_terms( $catalogue_product->get_id(), 'product_cat' );
    $term  = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
    if ( is_product_category() || is_tax( 'gamme' ) ) {
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
<div class="catalogue">
    <div class="catalogue__intro">
        <h1 class="catalogue__title"><?php echo is_shop() ? 'Nos Plats à Emporter' : esc_html( is_tax( 'gamme' ) ? 'Gamme ' . single_term_title( '', false ) : woocommerce_page_title( false ) ); ?></h1>
        <?php afficher_lien_panier(); ?>
    </div>
    <?php get_template_part( 'templates/gammes' ); ?>
    <?php woocommerce_output_all_notices(); ?>
    <p class="catalogue__feedback" role="status" aria-live="polite" aria-atomic="true"></p>
    <?php if ( ! $groups ) : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
    <?php endif; ?>
    <?php $section_index = 0; ?>
    <?php foreach ( $groups as $group_id => $group ) :
        $section_class = 'catalogue__section';
        $category_class = 'catalogue__category';
        if ( $section_index > 0 ) {
            $section_class .= ' catalogue__section--following';
            $category_class .= ' catalogue__category--following';
        }
        if ( $section_index > 1 ) {
            $section_class .= ' catalogue__section--spaced';
        }
        ++$section_index;
        ?>
        <section class="<?php echo esc_attr( $section_class ); ?>" aria-labelledby="category-<?php echo esc_attr( $group_id ); ?>">
            <h2 class="<?php echo esc_attr( $category_class ); ?>" id="category-<?php echo esc_attr( $group_id ); ?>"><?php echo esc_html( is_tax( 'gamme' ) ? 'Les produits de cette gamme' : 'Nos ' . $group['name'] ); ?></h2>
            <ul class="catalogue__grid" role="list" aria-label="<?php echo esc_attr( is_tax( 'gamme' ) ? 'Produits de la gamme ' . $group['name'] : 'Produits : ' . $group['name'] . '. Sur petit écran, faire défiler horizontalement pour voir les autres produits.' ); ?>">
                <?php foreach ( $group['items'] as $index => $produit ) : ?>
                    <?php get_template_part( 'templates/carte-produit', null, array(
                        'produit'   => $produit,
                        'catalogue' => true,
                        'plat'      => has_term( array( 'plat', 'plats' ), 'product_cat', $produit->get_id() ),
                        'index'     => $index,
                        'quantite'  => $counts[ (string) $produit->get_id() ] ?? 0,
                    ) ); ?>
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
