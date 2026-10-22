<?php
/** Carte commune au catalogue, aux suggestions et aux nouveautés. */
defined( 'ABSPATH' ) || exit;
$produit = $args['produit'];
$id = $produit->get_id();
$nom = $produit->get_name();
$url = $produit->get_permalink();
$catalogue = ! empty( $args['catalogue'] );
$plat = ! empty( $args['plat'] );
$index = $args['index'] ?? 0;
$quantite = $args['quantite'] ?? 0;
$disponible = $produit->is_purchasable() && $produit->is_in_stock();

$description = $produit->get_short_description() ?: $produit->get_description();
$classe = 'carte';
if ( $catalogue ) {
    $classe .= $plat ? ' carte--plat' : '';
} else {
    $classe .= ' carte--suggestion';
    $composition = function_exists( 'get_field' ) ? get_field( 'composition', $id ) : '';
    $description = $composition ?: $produit->get_short_description();
}
if ( ! empty( $args['nouveaute'] ) ) {
    $classe .= ' carte--nouveaute';
}
?>
<li class="<?php echo esc_attr( $classe ); ?>" data-product-id="<?php echo esc_attr( $id ); ?>">
    <a class="carte__lien_image" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $nom ); ?>">
        <?php echo $produit->get_image( $catalogue ? 'full' : 'large', array( 'class' => 'carte__image' ) ); ?>
    </a>
    <div class="carte__contenu">
        <div class="carte__texte">
            <div class="carte__entete">
                <h3 class="carte__titre<?php echo $plat && 0 === $index ? ' carte__titre--compact' : ''; ?>"><a class="carte__lien" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $nom ); ?></a></h3>
                <div class="carte__prix"><?php echo wp_kses_post( $produit->get_price_html() ); ?></div>
            </div>
            <p class="carte__description<?php echo $plat && $index < 2 ? ' carte__description--compact' : ''; ?>"><?php echo esc_html( wp_strip_all_tags( $description ) ); ?></p>
        </div>
        <?php if ( $catalogue && $produit->is_type( 'simple' ) ) : ?>
            <div class="quantite" role="group" aria-label="<?php echo esc_attr( 'Quantité de ' . $nom . ' dans le panier' ); ?>" data-purchasable="<?php echo $disponible ? '1' : '0'; ?>" data-max="<?php echo esc_attr( $produit->get_max_purchase_quantity() ); ?>">
                <button class="quantite__button quantite__button--minus bouton" type="button" data-delta="-1" aria-label="<?php echo esc_attr( 'Retirer une unité de ' . $nom ); ?>" disabled><span class="quantite__symbol" aria-hidden="true">−</span></button>
                <output class="quantite__value" aria-label="Quantité dans le panier"><?php echo esc_html( $quantite ); ?></output>
                <button class="quantite__button bouton" type="button" data-delta="1" aria-label="<?php echo esc_attr( 'Ajouter une unité de ' . $nom ); ?>" disabled><span class="quantite__symbol" aria-hidden="true">+</span></button>
            </div>
            <?php if ( ! $disponible ) : ?><p class="carte__disponibilite">Indisponible</p><?php endif; ?>
            <noscript><a href="<?php echo esc_url( $url ); ?>">Choisir la quantité sur la fiche produit</a></noscript>
        <?php else : ?>
            <a class="button bouton carte__bouton bouton--orange" href="<?php echo esc_url( $url ); ?>"><?php echo $produit->is_type( 'variable' ) || $catalogue ? 'Choisir les options' : 'Voir le produit'; ?></a>
        <?php endif; ?>
    </div>
</li>
