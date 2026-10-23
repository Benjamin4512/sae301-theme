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
            <?php get_template_part( 'templates/carte-membre', null, array(
                'identite'     => $identite,
                'presentation' => $presentation,
            ) ); ?>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
