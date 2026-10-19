<?php
defined( 'ABSPATH' ) || exit;
get_header();
$presentations = array(
    'Patron du restaurant' => 'Passionné de restauration, il fait vivre l’esprit du Gaulois : une cuisine généreuse et un accueil chaleureux pour partager un bon moment.',
    'Chef cuisinier' => 'Aux commandes des fourneaux, il prépare une cuisine française faite maison et des grillades savoureuses, avec soin et passion.',
    'Responsable de salle' => 'À votre écoute dès votre arrivée, elle coordonne le service et veille à ce que chaque repas se déroule dans une ambiance conviviale.',
);
$membres = new WP_Query( array(
    'post_type'      => 'membre',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'       => 'date',
    'order'         => 'ASC',
) );
?>
<main id="main" class="sae-team">
    <h1 class="sae-team__title">Notre équipe</h1>
    <div class="sae-team__list">
        <?php while ( $membres->have_posts() ) : $membres->the_post();
            $identite = explode( ' – ', html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ), 2 );
            $presentation = $presentations[ $identite[1] ?? '' ] ?? get_the_excerpt();
        ?>
            <article class="sae-team__card">
                <a class="sae-team__portrait" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Voir le profil de ' . $identite[0] ); ?>">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'sae-team__image', 'alt' => $identite[0] ) ); ?>
                </a>
                <div class="sae-team__content">
                    <h2 class="sae-team__name"><?php echo esc_html( $identite[0] ); ?></h2>
                    <?php if ( ! empty( $identite[1] ) ) : ?>
                        <p class="sae-team__role"><?php echo esc_html( $identite[1] ); ?></p>
                    <?php endif; ?>
                    <p class="sae-team__description"><?php echo esc_html( $presentation ); ?></p>
                    <a class="sae-team__button" href="<?php the_permalink(); ?>">Voir plus<span class="screen-reader-text"> : <?php echo esc_html( $identite[0] ); ?></span></a>
                </div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</main>
<?php get_footer(); ?>
