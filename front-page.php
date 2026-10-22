<?php
/** Page d'accueil statique et trois derniers produits du catalogue. */
defined( 'ABSPATH' ) || exit;
get_header();

$nouveautes = function_exists( 'wc_get_products' ) ? wc_get_products( array(
    'status'     => 'publish',
    'visibility' => 'catalog',
    'limit'      => 3,
    'orderby'    => 'date',
    'order'      => 'DESC',
) ) : array();
?>
<main id="main" class="accueil">
    <section class="accueil__introduction" aria-labelledby="bienvenue">
        <h1 class="accueil__titre" id="bienvenue">Bienvenue chez Le Gaulois</h1>
        <div class="accueil__description">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="accueil__nouveautes" aria-labelledby="nouveautes">
        <h2 class="accueil__titre" id="nouveautes">Les Nouveautés de notre carte</h2>
        <p class="accueil__sous_titre">Découvrir nos nouvelles créations 100% fait-maison!</p>
        <?php if ( $nouveautes ) : ?>
            <ul class="accueil__produits" role="list">
                <?php foreach ( $nouveautes as $nouveaute ) : ?>
                    <?php get_template_part( 'templates/carte-produit', null, array(
                        'produit'   => $nouveaute,
                        'nouveaute' => true,
                    ) ); ?>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="accueil__vide">Nos nouveautés arrivent bientôt.</p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
