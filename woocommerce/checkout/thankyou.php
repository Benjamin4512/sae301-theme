<?php
defined( 'ABSPATH' ) || exit;

if ( ! $order || $order->has_status( 'failed' ) ) {
    include WC()->plugin_path() . '/templates/checkout/thankyou.php';
    return;
}

do_action( 'woocommerce_before_thankyou', $order->get_id() );
$paid = $order->is_paid();
$method = $order->get_payment_method();
$shipping = $order->get_shipping_method();
$pickup = (bool) preg_match( '/retrait|collect|pickup/i', $shipping );
$title = $order->has_status( array( 'cancelled', 'refunded' ) ) ? 'Détails de la commande' : 'Commande validée';
$notice = $paid ? 'Paiement reçu' : 'Paiement en attente';
if ( ! $paid && 'bacs' === $method ) {
    $notice = 'Virement bancaire en attente';
} elseif ( ! $paid && 'cod' === $method ) {
    $notice = 'Paiement prévu au retrait';
}
if ( $order->has_status( array( 'cancelled', 'refunded' ) ) ) {
    $notice = wc_get_order_status_name( $order->get_status() );
}
$totals = $order->get_order_item_totals();
$phone = $order->get_billing_phone() ?: $order->get_shipping_phone();
?>
<section class="sae-receipt">
    <nav class="sae-receipt__breadcrumb" aria-label="Fil d’Ariane">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page"><?php echo esc_html( $title ); ?></span>
    </nav>

    <header class="sae-receipt__header">
        <h1 class="sae-receipt__title"><?php echo esc_html( $title ); ?></h1>
        <p class="sae-receipt__notice"><span class="sae-receipt__check" aria-hidden="true">✓</span><?php echo esc_html( $notice ); ?></p>
    </header>

    <dl class="sae-receipt__overview">
        <div class="sae-receipt__fact"><dt>N° de commande</dt><dd><?php echo esc_html( $order->get_order_number() ); ?></dd></div>
        <div class="sae-receipt__fact"><dt>Statut</dt><dd><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></dd></div>
        <div class="sae-receipt__fact"><dt>Paiement</dt><dd><?php echo esc_html( $order->get_payment_method_title() ); ?></dd></div>
        <div class="sae-receipt__fact"><dt><?php echo $pickup ? 'Retrait' : 'Livraison'; ?></dt><dd><?php echo esc_html( $pickup ? 'Sur place' : ( $shipping ?: 'Non applicable' ) ); ?></dd></div>
    </dl>

    <div class="sae-receipt__layout">
        <section class="sae-receipt__summary" aria-labelledby="receipt-summary">
            <h2 class="sae-receipt__heading" id="receipt-summary">Votre commande</h2>
            <ul class="sae-receipt__products">
                <?php foreach ( $order->get_items() as $item ) :
                    $product = $item->get_product();
                    ?>
                    <li class="sae-receipt__product">
                        <?php if ( $product ) : ?>
                            <?php echo $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'sae-receipt__image', 'alt' => $item->get_name() ) ); ?>
                        <?php else : ?>
                            <img class="sae-receipt__image" src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="">
                        <?php endif; ?>
                        <div class="sae-receipt__product-info">
                            <p class="sae-receipt__product-name"><?php echo esc_html( $item->get_name() ); ?></p>
                            <p class="sae-receipt__product-unit">× <?php echo esc_html( $item->get_quantity() ); ?> · <?php echo wp_kses_post( wc_price( $order->get_item_subtotal( $item, true ), array( 'currency' => $order->get_currency() ) ) ); ?></p>
                            <?php wc_display_item_meta( $item ); ?>
                        </div>
                        <span class="sae-receipt__product-total"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <dl class="sae-receipt__totals">
                <?php foreach ( $totals as $key => $total ) :
                    if ( in_array( $key, array( 'order_total', 'payment_method' ), true ) ) {
                        continue;
                    }
                    if ( 'shipping' === $key ) {
                        $total['label'] = $pickup ? 'Retrait sur place' : 'Livraison';
                        $shipping_total = (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
                        $total['value'] = 0.0 === $shipping_total ? 'Gratuit' : wc_price( $shipping_total, array( 'currency' => $order->get_currency() ) );
                    }
                    ?>
                    <div class="sae-receipt__total-row"><dt><?php echo esc_html( rtrim( wp_strip_all_tags( $total['label'] ), ' :' ) ); ?></dt><dd><?php echo wp_kses_post( $total['value'] ); ?></dd></div>
                <?php endforeach; ?>
            </dl>
            <div class="sae-receipt__grand-total"><span><?php echo $paid ? 'Total payé' : 'Total de la commande'; ?></span><span><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span></div>
        </section>

        <div class="sae-receipt__customer">
            <section class="sae-receipt__contact" aria-labelledby="receipt-contact">
                <h2 class="sae-receipt__heading" id="receipt-contact">Vos coordonnées</h2>
                <dl class="sae-receipt__contact-grid">
                    <div class="sae-receipt__fact"><dt>Nom</dt><dd><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></dd></div>
                    <div class="sae-receipt__fact"><dt>Téléphone</dt><dd><?php echo esc_html( $phone ?: 'Non renseigné' ); ?></dd></div>
                    <div class="sae-receipt__email sae-receipt__fact"><dt>E-mail</dt><dd><?php echo esc_html( $order->get_billing_email() ); ?></dd></div>
                </dl>
            </section>

            <div class="sae-receipt__addresses">
                <section class="sae-receipt__address">
                    <h2 class="sae-receipt__heading">Adresse de facturation</h2>
                    <address><?php echo wp_kses_post( $order->get_formatted_billing_address() ?: 'Non renseignée' ); ?></address>
                </section>
                <section class="sae-receipt__address">
                    <h2 class="sae-receipt__heading"><?php echo $pickup ? 'Lieu de retrait' : 'Adresse de livraison'; ?></h2>
                    <?php if ( $pickup ) : ?>
                        <address>Restaurant le Gaulois<br>148 rue de Bourgogne<br>45000 ORLEANS</address>
                        <a class="sae-receipt__phone" href="tel:+33608881632">Tel : 06 08 88 16 32</a>
                    <?php else : ?>
                        <address><?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ); ?></address>
                    <?php endif; ?>
                </section>
            </div>

            <?php
            $details_priority = has_action( 'woocommerce_thankyou', 'woocommerce_order_details_table' );
            if ( false !== $details_priority ) {
                remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', $details_priority );
            }
            ob_start();
            do_action( 'woocommerce_thankyou_' . $method, $order->get_id() );
            do_action( 'woocommerce_thankyou', $order->get_id() );
            $instructions = ob_get_clean();
            if ( false !== $details_priority ) {
                add_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', $details_priority );
            }
            if ( trim( $instructions ) ) : ?>
                <details class="sae-receipt__instructions">
                    <summary>Instructions de paiement</summary>
                    <div class="sae-receipt__instructions-content"><?php echo $instructions; ?></div>
                </details>
            <?php endif; ?>

            <a class="sae-receipt__button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l’accueil</a>
        </div>
    </div>
</section>
