<?php

/** Présentation des contenus du restaurant. */
defined('ABSPATH') || exit;
$brand_assets = get_stylesheet_directory_uri() . '/assets';
$brand_shop   = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
$brand_team   = get_post_type_archive_link('membre') ?: home_url('/membres/');
?>
<div class="header header--compact">
    <div class="header__overlay" aria-hidden="true"></div>
    <div class="header__inner">
        <a class="header__logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Le Gaulois — Accueil"><img class="header__image" src="<?php echo esc_url($brand_assets . '/images/logo-gaulois.png'); ?>" alt="Le Gaulois Restaurant &amp; Grill" width="190" height="190" fetchpriority="high"></a>
        <nav class="header__navigation" aria-label="Navigation principale" id="site-navigation">
            <ul class="header__links">
                <li class="header__item"><a class="header__link" href="<?php echo esc_url($brand_shop); ?>">COMMANDER EN LIGNE</a></li>
                <?php if (function_exists('wc_get_cart_url')) : ?>
                    <li class="header__item"><a class="header__link" href="<?php echo esc_url(wc_get_cart_url()); ?>">MON PANIER</a></li>
                    <li class="header__item"><a class="header__link" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">MON COMPTE</a></li>
                <?php endif; ?>
                <li class="header__item"><a class="header__link" href="<?php echo esc_url($brand_team); ?>">L’ÉQUIPE</a></li>
            </ul>
            <details class="menu">
                <summary class="menu__toggle">Menu<span class="menu__icon" aria-hidden="true">☰</span></summary>
                <ul class="menu__links">
                    <li class="menu__item"><a class="menu__link" href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <li class="menu__item"><a class="menu__link" href="<?php echo esc_url($brand_shop); ?>">Commander en ligne</a></li>
                    <?php if (function_exists('wc_get_cart_url')) : ?>
                        <li class="menu__item"><a class="menu__link" href="<?php echo esc_url(wc_get_cart_url()); ?>">Mon panier</a></li>
                        <li class="menu__item"><a class="menu__link" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Mon compte</a></li>
                    <?php endif; ?>
                    <li class="menu__item"><a class="menu__link" href="<?php echo esc_url($brand_team); ?>">L’équipe</a></li>
                </ul>
            </details>
        </nav>
        <a class="header__booking" href="tel:+33608881632">RÉSERVEZ AU 06 08 88 16 32</a>
    </div>
</div>
