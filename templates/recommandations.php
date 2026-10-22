<?php
defined( 'ABSPATH' ) || exit;
$suggestions = $args['products'];
?>
<section class="suggestions" aria-labelledby="recommendations_title">
    <h2 class="suggestions__title" id="recommendations_title">Vous aimerez aussi</h2>
    <ul class="suggestions__grid" role="list">
        <?php foreach ( $suggestions as $suggestion ) : ?>
            <?php get_template_part( 'templates/carte-produit', null, array( 'produit' => $suggestion ) ); ?>
        <?php endforeach; ?>
    </ul>
</section>
