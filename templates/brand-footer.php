<?php
// footer
defined( 'ABSPATH' ) || exit;
$brand_assets = get_stylesheet_directory_uri() . '/assets';
$brand_map = 'https://www.google.com/maps/search/?api=1&query=Restaurant+Le+Gaulois+148+rue+de+Bourgogne+45000+Orleans';
?>
<div class="footer">
    <a class="footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Le Gaulois — Accueil"><img class="footer__image" src="<?php echo esc_url( $brand_assets . '/images/logo-gaulois.png' ); ?>" alt="Le Gaulois Restaurant &amp; Grill" width="190" height="190" loading="lazy"></a>
    <section class="footer__contact" aria-labelledby="footer_contact">
        <h2 class="footer__heading" id="footer_contact">Coordonnées</h2>
        <address class="footer__address">Restaurant le Gaulois<br>148 rue de Bourgogne<br>45000 ORLEANS</address>
        <a class="footer__phone" href="tel:+33608881632">Tel : 06 08 88 16 32</a>
    </section>
    <nav class="footer__information" aria-labelledby="footer_information">
        <h2 class="footer__heading" id="footer_information">Informations</h2>
        <ul class="footer__links">
            <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( $brand_assets . '/documents/menu-gaulois.pdf' ); ?>">Voir le menu</a></li>
            <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( $brand_map ); ?>">Voir les avis</a></li>
            <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( $brand_map ); ?>">Nous situer</a></li>
            <li class="footer__item"><a class="footer__link" href="tel:+33608881632">Nous contacter</a></li>
        </ul>
    </nav>
    <div class="footer__promise"><img class="footer__icon" src="<?php echo esc_url( $brand_assets . '/images/picto-salade.png' ); ?>" width="87" height="85" alt="" loading="lazy"><p class="footer__tagline">Cuisine française<br>et traditionnelle<br>100% fait maison</p></div>
</div>
