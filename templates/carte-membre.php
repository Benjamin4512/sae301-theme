<?php
// carte membre
defined( 'ABSPATH' ) || exit;
$identite = $args['identite'];
$presentation = $args['presentation'];
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
        <a class="equipe__button bouton bouton--orange bouton--voir_plus" href="<?php the_permalink(); ?>">Voir plus<span class="screen-reader-text"> : <?php echo esc_html( $identite[0] ); ?></span></a>
    </div>
</article>
