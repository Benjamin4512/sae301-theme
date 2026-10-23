<?php
// onglet filtres
defined( 'ABSPATH' ) || exit;
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>
<aside class="filtres" id="filtres" aria-label="Filtrer les produits">
    <details class="filtres__panneau">
        <summary class="filtres__ouvrir">
            Filtrer les plats
            <img class="filtres__fleche" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/icons/filter-chevron.svg' ); ?>" alt="">
        </summary>
        <div class="filtres__contenu">
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
            <button class="filtres__fermer" type="button">
                Fermer
                <img class="filtres__fleche" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/icons/filter-chevron-up.svg' ); ?>" alt="">
            </button>
        </div>
    </details>
</aside>
