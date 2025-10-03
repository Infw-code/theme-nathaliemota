<?php
wp_enqueue_script(
    'lightbox',
    get_template_directory_uri() . '/js/lightbox.js',
    array('theme-nm-scripts'), 
    '1.0',
    true
);
// Charger CSS et JS du thème
function theme_nm_assets()
{
    // CSS principal
    wp_enqueue_style(
        'theme-nm-style',
        get_stylesheet_uri(),
        array(),
        '1.0'
    );

    // JS principal
    wp_enqueue_script(
        'theme-nm-scripts',
        get_template_directory_uri() . '/js/scripts.js',
        array(),
        '1.0',
        true
    );

    // Passer l’ajaxUrl au JS
    wp_localize_script('theme-nm-scripts', 'themeVars', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'theme_nm_assets');

// Enregistrer le menu principal
function theme_nm_register_menus()
{
    register_nav_menus(
        array(
            'main-menu' => __('Menu Principal')
        )
    );
}
add_action('after_setup_theme', 'theme_nm_register_menus');
function cf7_photo_references_scf() {
    // Récupère tous les posts 'photo'
    $photos = get_posts([
        'post_type' => 'photo',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    $options = [];
    foreach ($photos as $photo) {
        $ref = scf::get('reference', $photo->ID); // SCF récupère la référence
        if ($ref) {
            $options[] = esc_html($ref);
        }
    }

    return $options; // tableau d'options
}
/* Pour charger + d'images sur la page principale avec filtres */
function load_more_images() {
    $exclude = isset($_GET['exclude']) ? array_map('intval', explode(',', $_GET['exclude'])) : [];
    $posts_per_page = 8;

    // Récupérer les filtres
    $categorie = isset($_GET['categorie']) ? sanitize_text_field($_GET['categorie']) : '';
    $format = isset($_GET['format']) ? sanitize_text_field($_GET['format']) : '';
    $trier_par = isset($_GET['trier-par']) ? sanitize_text_field($_GET['trier-par']) : 'rand';

    $args = [
        'post_type'      => 'photo',
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'post__not_in'   => $exclude,
    ];

    // Tri
    if ($trier_par === 'date_asc') {
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
    } elseif ($trier_par === 'date_desc') {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    } else {
        $args['orderby'] = 'rand';
    }

    // Taxonomy filters
    $tax_query = [];
    if ($categorie) {
        $tax_query[] = [
            'taxonomy' => 'categorie',
            'field'    => 'slug',
            'terms'    => $categorie,
        ];
    }
    if ($format) {
        $tax_query[] = [
            'taxonomy' => 'format',
            'field'    => 'slug',
            'terms'    => $format,
        ];
    }
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $photos = get_posts($args);

    foreach ($photos as $photo) {
        $titre = get_the_title($photo->ID);

        // Récupérer la catégorie
        $categories = get_the_terms($photo->ID, 'categorie');
        $categorie_nom = !empty($categories) && !is_wp_error($categories) ? $categories[0]->name : '';

        // Récupérer la référence (champ personnalisé)
        $reference = get_post_meta($photo->ID, 'reference', true);
        $reference = $reference ? $reference : '';

        // Caption pour la lightbox : référence et catégorie
        $caption = esc_html($reference) . ' | ' . esc_html($categorie_nom);

        echo '<div class="gallery-item">';
        echo '<a href="' . get_permalink($photo->ID) . '" data-id="' . $photo->ID . '">';
        echo get_the_post_thumbnail($photo->ID, 'medium_large');
        echo '</a>';

        echo '<div class="gallery-overlay">';
        echo '<div class="overlay-eye">';
        echo '<a href="' . get_permalink($photo->ID) . '">';
        echo '<img src="' . get_template_directory_uri() . '/images/Group.png" alt="Voir la photo" class="eye-icon-img">';
        echo '</a></div>';

        echo '<div class="overlay-action">';
        echo '<span class="open-lightbox" 
                   data-image="' . esc_url(wp_get_attachment_url(get_post_thumbnail_id($photo->ID))) . '" 
                   data-caption="' . $caption . '">';
        echo '<img src="' . get_template_directory_uri() . '/images/Icon_fullscreen.png" alt="Voir en plein écran" class="fullscreen-icon">';
        echo '</span></div>';

        echo '<div class="overlay-text">';
        echo '<span class="photo-title">' . esc_html($titre) . '</span>';
        echo '<span class="photo-cat">' . esc_html($categorie_nom) . '</span>';
        echo '</div>';
        echo '</div>'; // .gallery-overlay
        echo '</div>'; // .gallery-item

        $exclude[] = $photo->ID;
    }

    // Vérifier s’il reste encore des images non affichées
    $remaining_photos = get_posts([
        'post_type'      => 'photo',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'post__not_in'   => $exclude,
    ]);

    if (empty($remaining_photos)) {
        echo '<!-- END_OF_GALLERY -->';
    }

    wp_die();
}
add_action('wp_ajax_load_more_images', 'load_more_images');
add_action('wp_ajax_nopriv_load_more_images', 'load_more_images');

/*ajout de filtres*/
add_action('init', function () {
    register_taxonomy('genre', ['post'], [
        'label' => 'Genres',
        'hierarchical' => true,
        'public' => true,
        'show_admin_column' => true,
        'query_var' => 'genre',
        'rewrite' => ['slug' => 'genre'],
    ]);
});

// CPT "photo"
function create_photo_cpt()
{
    $labels = array(
        'name' => 'Photos',
        'singular_name' => 'Photo',
        'menu_name' => 'Photos',
        'all_items' => 'Toutes les photos',
        'add_new_item' => 'Ajouter une photo',
        'edit_item' => 'Modifier la photo',
        'new_item' => 'Nouvelle photo',
        'view_item' => 'Voir la photo',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-image',
        'supports' => array('title', 'editor', 'thumbnail', 'author', 'excerpt'),
        'show_in_rest' => true, // utile si tu veux Gutenberg / API
    );

    register_post_type('photo', $args);
}
add_action('init', 'create_photo_cpt');

// Taxonomie "catégorie"
function create_photo_taxonomies()
{
    // Catégorie
    register_taxonomy('categorie', 'photo', array(
        'label' => 'Catégories',
        'hierarchical' => true,
        'show_in_rest' => true,
    ));

    // Format
    register_taxonomy('format', 'photo', array(
        'label' => 'Formats',
        'hierarchical' => false,
        'show_in_rest' => true,
    ));
}
add_action('init', 'create_photo_taxonomies');

add_theme_support('post-thumbnails');

add_action('admin_head', function () {
    echo '<style>
        #postimagediv { display: block !important; }
    </style>';
});
