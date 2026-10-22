<?php
/** Liste des membres et choix de leur plat préféré. */
defined( 'ABSPATH' ) || exit;

function preparer_liste_membres( $requete ) {
    if ( ! is_admin() && $requete->is_main_query() && $requete->is_post_type_archive( 'membre' ) ) {
        $requete->set( 'posts_per_page', -1 );
        $requete->set( 'post_status', 'publish' );
        $requete->set( 'orderby', 'date' );
        $requete->set( 'order', 'ASC' );
        $requete->set( 'no_found_rows', true );
    }
}
add_action( 'pre_get_posts', 'preparer_liste_membres' );

function ajouter_champ_plat_prefere() {
    add_meta_box( 'membre_plat_prefere', 'Plat préféré', 'afficher_champ_plat_prefere', 'membre', 'side' );
}
add_action( 'add_meta_boxes_membre', 'ajouter_champ_plat_prefere' );

function afficher_champ_plat_prefere( $membre ) {
    if ( ! function_exists( 'wc_get_products' ) ) {
        return;
    }
    wp_nonce_field( 'enregistrer_plat_prefere', 'plat_prefere_nonce' );
    $plat_id = (int) get_post_meta( $membre->ID, 'plat_prefere', true );
    // Le catalogue est chargé uniquement pour le choix en administration.
    $produits = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
    ?>
    <p><label for="plat_prefere">Produit lié au bouton « Découvrir » du profil.</label></p>
    <select name="plat_prefere" id="plat_prefere" class="widefat">
        <option value="0">Aucun produit</option>
        <?php foreach ( $produits as $produit ) : ?>
            <option value="<?php echo esc_attr( $produit->get_id() ); ?>" <?php selected( $plat_id, $produit->get_id() ); ?>><?php echo esc_html( $produit->get_name() ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

function enregistrer_plat_prefere( $membre_id ) {
    if ( ! isset( $_POST['plat_prefere_nonce'], $_POST['plat_prefere'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['plat_prefere_nonce'] ) ), 'enregistrer_plat_prefere' )
        || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        || ! current_user_can( 'edit_post', $membre_id ) ) {
        return;
    }
    $plat_id = absint( $_POST['plat_prefere'] );
    if ( $plat_id && 'product' !== get_post_type( $plat_id ) ) {
        return;
    }
    update_post_meta( $membre_id, 'plat_prefere', $plat_id );
}
add_action( 'save_post_membre', 'enregistrer_plat_prefere' );
