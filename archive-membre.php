<?php
defined( 'ABSPATH' ) || exit;
get_header();
$presentations = array(
    'Patron du restaurant' => 'Passionné de restauration, il fait vivre l’esprit du Gaulois : une cuisine généreuse et un accueil chaleureux pour partager un bon moment.',
    'Chef cuisinier' => 'Aux commandes des fourneaux, il prépare une cuisine française faite maison et des grillades savoureuses, avec soin et passion.',
    'Responsable de salle' => 'À votre écoute dès votre arrivée, elle coordonne le service et veille à ce que chaque repas se déroule dans une ambiance conviviale.',
);
?>
<main id="main" class="equipe">
    <h1 class="equipe__title">Notre équipe</h1>
    <div class="equipe__list">
        <?php while ( have_posts() ) : the_post();
            $identite = explode( ' – ', html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ), 2 );
            $presentation = $presentations[ $identite[1] ?? '' ] ?? get_the_excerpt();
        ?>
            <article class="equipe__card">
                <a class="equipe__portrait" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Voir le profil de ' . $identite[0] ); ?>">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'equipe__image' . ( ( $identite[1] ?? '' ) === 'Responsable de salle' ? ' equipe__image--decale' : '' ), 'alt' => $identite[0] ) ); ?>
                </a>
                <div class="equipe__content">
                    <h2 class="equipe__name"><?php echo esc_html( $identite[0] ); ?></h2>
                    <?php if ( ! empty( $identite[1] ) ) : ?>
                        <p class="equipe__role"><?php echo esc_html( $identite[1] ); ?></p>
                    <?php endif; ?>
                    <p class="equipe__description"><?php echo esc_html( $presentation ); ?></p>
                    <a class="equipe__button bouton bouton--orange" href="<?php the_permalink(); ?>">Voir plus<span class="screen-reader-text"> : <?php echo esc_html( $identite[0] ); ?></span></a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
