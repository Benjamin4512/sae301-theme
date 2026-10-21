<?php
defined( 'ABSPATH' ) || exit;
$suggestions = $args['products'];
?>
<section class="sae_suggestions" aria-labelledby="sae_recommendations_title">
    <h2 class="sae_suggestions__title" id="sae_recommendations_title">Vous aimerez aussi</h2>
    <ul class="sae_suggestions__grid" role="list">
        <?php foreach ( $suggestions as $suggestion ) :
            $id = $suggestion->get_id();
            $url = $suggestion->get_permalink();
            $description = function_exists( 'get_field' ) ? get_field( 'composition', $id ) : '';
            $description = $description ?: $suggestion->get_short_description();
        ?>
            <li class="sae_carte sae_carte--suggestion" data-product-id="<?php echo esc_attr( $id ); ?>">
                <a class="sae_carte__image_link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $suggestion->get_name() ); ?>">
                    <?php echo $suggestion->get_image( 'large', array( 'class' => 'sae_carte__image' ) ); ?>
                </a>
                <div class="sae_carte__content">
                    <div class="sae_carte__text">
                        <div class="sae_carte__heading">
                            <h3 class="sae_carte__title"><a class="sae_carte__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $suggestion->get_name() ); ?></a></h3>
                            <div class="sae_carte__price"><?php echo wp_kses_post( $suggestion->get_price_html() ); ?></div>
                        </div>
                        <p class="sae_carte__description"><?php echo esc_html( wp_strip_all_tags( $description ) ); ?></p>
                    </div>
                    <a class="button sae_carte__options sae_bouton--orange" href="<?php echo esc_url( $url ); ?>"><?php echo $suggestion->is_type( 'variable' ) ? 'Choisir les options' : 'Voir le produit'; ?></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
