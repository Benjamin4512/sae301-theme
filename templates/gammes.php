<?php
defined( 'ABSPATH' ) || exit;
$gammes = get_terms( array( 'taxonomy' => 'gamme', 'hide_empty' => false ) );
if ( is_wp_error( $gammes ) || ! $gammes ) {
    return;
}
?>
<nav class="sae-gammes" aria-label="Parcourir les gammes">
    <p class="sae-gammes__title">Nos gammes</p>
    <div class="sae-gammes__links">
        <a class="sae-gammes__link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"<?php if ( is_shop() ) : ?> aria-current="page"<?php endif; ?>>Tous les produits</a>
        <?php foreach ( $gammes as $gamme ) :
            $url = get_term_link( $gamme );
            if ( is_wp_error( $url ) ) {
                continue;
            }
        ?>
            <a class="sae-gammes__link" href="<?php echo esc_url( $url ); ?>"<?php if ( is_tax( 'gamme', $gamme->term_id ) ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $gamme->name ); ?></a>
        <?php endforeach; ?>
    </div>
</nav>
