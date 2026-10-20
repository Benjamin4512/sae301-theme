<?php
defined( 'ABSPATH' ) || exit;
$suggestions = $args['products'];
?>
<section class="sae-recommendations" aria-labelledby="sae-recommendations-title">
    <h2 class="sae-recommendations__title" id="sae-recommendations-title">Vous aimerez aussi</h2>
    <ul class="sae-recommendations__grid" role="list">
        <?php foreach ( $suggestions as $suggestion ) :
            $id = $suggestion->get_id();
            $url = $suggestion->get_permalink();
            $description = function_exists( 'get_field' ) ? get_field( 'composition', $id ) : '';
            $description = $description ?: $suggestion->get_short_description();
        ?>
            <li class="sae-card" data-product-id="<?php echo esc_attr( $id ); ?>">
                <a class="sae-card__image-link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $suggestion->get_name() ); ?>">
                    <?php echo $suggestion->get_image( 'large', array( 'class' => 'sae-card__image' ) ); ?>
                </a>
                <div class="sae-card__content">
                    <div class="sae-card__text">
                        <div class="sae-card__heading">
                            <h3 class="sae-card__title"><a class="sae-card__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $suggestion->get_name() ); ?></a></h3>
                            <div class="sae-card__price"><?php echo wp_kses_post( $suggestion->get_price_html() ); ?></div>
                        </div>
                        <p class="sae-card__description"><?php echo esc_html( wp_strip_all_tags( $description ) ); ?></p>
                    </div>
                    <a class="button sae-card__options" href="<?php echo esc_url( $url ); ?>"><?php echo $suggestion->is_type( 'variable' ) ? 'Choisir les options' : 'Voir le produit'; ?></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
