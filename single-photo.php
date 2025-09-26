<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <article class="photo-single">
            <div class="photo-info-wrapper">
                <!-- Colonne gauche : titre + infos -->
                <div class="photo-info-left">
                    <h1><?php the_title(); ?></h1>
                    <ul class="photo-meta">
                        <?php if ($reference = get_post_meta(get_the_ID(), 'reference', true)) : ?>
                            <li>RÉFÉRENCE : <?php echo esc_html($reference); ?></li>
                        <?php endif; ?>
                        <?php if ($categories = get_the_terms(get_the_ID(), 'categorie')) : ?>
                            <li>CATÉGORIE : <?php echo esc_html(implode(', ', wp_list_pluck($categories, 'name'))); ?></li>
                        <?php endif; ?>
                        <?php if ($formats = get_the_terms(get_the_ID(), 'format')) : ?>
                            <li>FORMAT : <?php echo esc_html(implode(', ', wp_list_pluck($formats, 'name'))); ?></li>
                        <?php endif; ?>
                        <?php if ($type = get_post_meta(get_the_ID(), 'type', true)) : ?>
                            <li>TYPE : <?php echo esc_html($type); ?></li>
                        <?php endif; ?>
                        <li>ANNÉE : <?php echo get_the_date('Y'); ?></li>
                    </ul>
                </div>

                <!-- Colonne droite : photo -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="photo-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="separator"></div>

            <!-- Wrapper bouton + navigation sur la même ligne -->
            <div class="photo-action-navigation">

                <!-- Bouton d'intérêt -->
                <div class="photo-interest">
                    <p>Cette photo vous intéresse ?</p>
                    <button type="button" class="open-modal" data-reference="<?php echo esc_attr(get_post_meta(get_the_ID(), 'reference', true)); ?>">
                        Contact
                    </button>
                </div>

                <!-- Navigation photos -->
                <?php
                $prev = get_previous_post();
                $next = get_next_post();

                // Bouclage circulaire
                if (!$prev) {
                    $last = get_posts([
                        'post_type' => 'photo',
                        'posts_per_page' => 1,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ]);
                    $prev = $last ? $last[0] : null;
                }

                if (!$next) {
                    $first = get_posts([
                        'post_type' => 'photo',
                        'posts_per_page' => 1,
                        'orderby' => 'date',
                        'order' => 'ASC',
                    ]);
                    $next = $first ? $first[0] : null;
                }
                ?>

                <div class="photo-navigation">
                    <div class="photo-preview">
                        <?php if ($next && has_post_thumbnail($next->ID)) echo get_the_post_thumbnail($next->ID, 'thumbnail'); ?>
                    </div>
                    <div class="photo-arrows">
                        <?php if ($prev): ?>
                            <a href="<?php echo get_permalink($prev->ID); ?>" class="prev-photo">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/Line6.png" alt="Précédent">
                            </a>
                        <?php endif; ?>
                        <?php if ($next): ?>
                            <a href="<?php echo get_permalink($next->ID); ?>" class="next-photo">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/Line7.png" alt="Suivant">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <div class="separator2"></div>

<!-- Photos similaires -->
<?php
$categories = wp_get_post_terms(get_the_ID(), 'categorie', ['fields' => 'ids']);
if (!empty($categories)):
    $related_args = [
        'post_type'      => 'photo',
        'posts_per_page' => 2,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
        'tax_query'      => [[
            'taxonomy' => 'categorie',
            'field'    => 'term_id',
            'terms'    => $categories,
        ]],
    ];
    $related_photos = new WP_Query($related_args);
    if ($related_photos->have_posts()):
        echo '<div class="related-photos">';
        echo '<h3>VOUS AIMEREZ AUSSI</h3>';
        echo '<div class="related-photos-grid">';
        while ($related_photos->have_posts()): $related_photos->the_post();
            $titre = get_the_title();
            $image_url = wp_get_attachment_url(get_post_thumbnail_id());
            $categories_terms = get_the_terms(get_the_ID(), 'categorie');
            $categorie_nom = $categories_terms && !is_wp_error($categories_terms) ? $categories_terms[0]->name : '';

            // Récupère la référence (ACF) ou le titre si vide
            $reference = get_field('reference');
            $caption_text = ($reference ? $reference : $titre) . ' - ' . $categorie_nom;

            echo '<div class="gallery-item related-photo-item">';

            // Image principale cliquable vers le post
            echo '<a href="' . get_permalink() . '" data-id="' . get_the_ID() . '">';
            if (has_post_thumbnail()) the_post_thumbnail('medium_large');
            echo '</a>';

            // Overlay spécifique à la single
            echo '<div class="related-overlay">';

            // Petit icône œil pour aller sur le post
            echo '<div class="overlay-eye">';
            echo '<a href="' . get_permalink() . '">';
            echo '<img src="' . get_template_directory_uri() . '/images/Group.png" alt="Voir la photo" class="eye-icon-img">';
            echo '</a>';
            echo '</div>';

            // Icône pour lightbox
            echo '<div class="overlay-action">';
            echo '<span class="open-lightbox" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($caption_text) . '">';
            echo '<img src="' . get_template_directory_uri() . '/images/Icon_fullscreen.png" alt="Voir en plein écran" class="fullscreen-icon">';
            echo '</span>';
            echo '</div>';

            // Texte overlay (titre + catégorie)
            echo '<div class="overlay-text-single">';
            echo '<span class="photo-title">' . esc_html($titre) . '</span>';
            echo '<span class="photo-cat">' . esc_html($categorie_nom) . '</span>';
            echo '</div>';

            echo '</div>'; // .related-overlay
            echo '</div>'; // .gallery-item

        endwhile;
        echo '</div></div>';
    endif;
    wp_reset_postdata();
endif;
?>
        </article>

<?php endwhile;
endif; ?>

<?php get_footer(); ?>