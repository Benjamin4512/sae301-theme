<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
    $identite = explode( ' – ', html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ), 2 );
    $description = strtolower( remove_accents( str_replace( '’', "'", wp_strip_all_tags( get_the_content() ) ) ) );
    $plat = null;
    if ( function_exists( 'wc_get_products' ) ) {
        $produits = wc_get_products( array( 'status' => 'publish', 'limit' => -1 ) );
        foreach ( $produits as $produit ) {
            $nom = strtolower( remove_accents( str_replace( '’', "'", $produit->get_name() ) ) );
            if ( false !== strpos( $description, $nom ) ) {
                $plat = $produit;
                break;
            }
        }
    }
?>
<main id="main" class="sae-member">
    <article class="sae-member__profile">
        <div class="sae-member__portrait">
            <?php the_post_thumbnail( 'full', array( 'class' => 'sae-member__image', 'alt' => $identite[0], 'fetchpriority' => 'high' ) ); ?>
        </div>
        <div class="sae-member__details">
            <h1 class="sae-member__name"><?php echo esc_html( $identite[0] ); ?></h1>
            <?php if ( ! empty( $identite[1] ) ) : ?>
                <p class="sae-member__role"><?php echo esc_html( $identite[1] ); ?></p>
            <?php endif; ?>
            <div class="sae-member__description"><?php the_content(); ?></div>
            <div class="sae-member__actions">
                <?php if ( $plat ) : ?>
                    <a class="sae-member__button" href="<?php echo esc_url( $plat->get_permalink() ); ?>"><?php echo esc_html( 'Découvrir ' . ( str_starts_with( $plat->get_name(), 'Côtes' ) ? 'les ' : 'la ' ) . $plat->get_name() ); ?></a>
                <?php endif; ?>
                <a class="sae-member__back" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) ); ?>">← Retour à l’équipe</a>
            </div>
        </div>
    </article>
</main>
<?php endwhile; get_footer(); ?>
