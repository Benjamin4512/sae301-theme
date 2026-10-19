<?php
/** Je presente le restaurant et les liens communs dans mon en-tete. */
defined( 'ABSPATH' ) || exit;
$brand_assets = get_stylesheet_directory_uri() . '/assets';
$brand_shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$brand_team   = get_post_type_archive_link( 'membre' ) ?: home_url( '/membres/' );
?>
<div class="sae-hero">
    <div class="sae-hero__overlay" aria-hidden="true"></div>
    <div class="sae-hero__inner">
        <a class="sae-hero__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Le Gaulois — Accueil"><img class="sae-hero__image" src="<?php echo esc_url( $brand_assets . '/images/logo-gaulois.png' ); ?>" alt="Le Gaulois Restaurant &amp; Grill" width="190" height="190" fetchpriority="high"></a>
        <nav class="sae-hero__navigation" aria-label="Navigation principale" id="site-navigation">
            <ul class="sae-hero__links">
                <li class="sae-hero__item"><a class="sae-hero__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">RESTAURANT LE GAULOIS</a></li>
                <li class="sae-hero__item"><a class="sae-hero__link" href="<?php echo esc_url( $brand_shop ); ?>">MENU</a></li>
                <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
                    <li class="sae-hero__item"><a class="sae-hero__link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">MON PANIER</a></li>
                    <li class="sae-hero__item"><a class="sae-hero__link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">MON COMPTE</a></li>
                <?php endif; ?>
                <li class="sae-hero__item"><a class="sae-hero__link" href="<?php echo esc_url( $brand_team ); ?>">L’ÉQUIPE</a></li>
            </ul>
            <details class="sae-menu">
                <summary class="sae-menu__toggle">Menu<span class="sae-menu__icon" aria-hidden="true">☰</span></summary>
                <ul class="sae-menu__links">
                    <li class="sae-menu__item"><a class="sae-menu__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a></li>
                    <li class="sae-menu__item"><a class="sae-menu__link" href="<?php echo esc_url( $brand_shop ); ?>">Nos plats à emporter</a></li>
                    <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
                        <li class="sae-menu__item"><a class="sae-menu__link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">Mon panier</a></li>
                        <li class="sae-menu__item"><a class="sae-menu__link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Mon compte</a></li>
                    <?php endif; ?>
                    <li class="sae-menu__item"><a class="sae-menu__link" href="<?php echo esc_url( $brand_team ); ?>">L’équipe</a></li>
                </ul>
            </details>
        </nav>
        <div class="sae-hero__text">
            <p class="sae-hero__eyebrow">RESTAURANT À ORLÉANS CUISINE 100% FAIT MAISON</p>
            <p class="sae-hero__description sae-hero__description--mobile">Cuisine traditionnelle<br>Française<br>&amp; Restaurant Grill Orléans<br>tapas, moules frites, et<br>autres spécialités!</p>
            <p class="sae-hero__description sae-hero__description--desktop">Cuisine traditionnelle<br>Française<br>&amp; Restaurant Grill<br>Orléans tapas, moules<br>frites, et autres<br>spécialités!</p>
        </div>
        <a class="sae-hero__booking" href="tel:+33608881632">RÉSERVEZ AU 06 08 88 16 32</a>
    </div>
</div>
