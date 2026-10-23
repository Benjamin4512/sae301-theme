<?php
// page d'un membre spécifique
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
    $identite = explode( ' – ', html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ), 2 );
    $plat_id = (int) get_post_meta( get_the_ID(), 'plat_prefere', true );
    $plat = $plat_id && function_exists( 'wc_get_product' ) ? wc_get_product( $plat_id ) : false;
?>
<main id="main" class="membre">
    <article class="membre__profile">
        <div class="membre__portrait">
            <?php the_post_thumbnail( 'full', array( 'class' => 'membre__image' . ( ( $identite[1] ?? '' ) === 'Responsable de salle' ? ' membre__image--decale' : '' ), 'alt' => $identite[0], 'fetchpriority' => 'high' ) ); ?>
        </div>
        <div class="membre__details">
            <h1 class="membre__name"><?php echo esc_html( $identite[0] ); ?></h1>
            <?php if ( ! empty( $identite[1] ) ) : ?>
                <p class="membre__role"><?php echo esc_html( $identite[1] ); ?></p>
            <?php endif; ?>
            <div class="membre__description"><?php the_content(); ?></div>
            <div class="membre__actions">
                <?php if ( $plat && $plat->get_status() === 'publish' && $plat->is_visible() ) : ?>
                    <a class="membre__button bouton bouton--orange" href="<?php echo esc_url( $plat->get_permalink() ); ?>"><?php echo esc_html( 'Découvrir ' . ( str_starts_with( $plat->get_name(), 'Côtes' ) ? 'les ' : 'la ' ) . $plat->get_name() ); ?></a>
                <?php endif; ?>
                <a class="membre__back" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) ); ?>">← Retour à l’équipe</a>
            </div>
        </div>
    </article>
</main>
<?php endwhile; get_footer(); ?>
