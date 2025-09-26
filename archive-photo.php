<?php get_header(); ?>

<h1>Toutes les Photos</h1>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<article class="photo-archive">

  <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

  <?php if ( has_post_thumbnail() ) : ?>
    <div class="photo-image">
      <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
    </div>
  <?php endif; ?>

  <ul class="photo-meta">
    <?php if ( $categories = get_the_terms(get_the_ID(), 'categorie') ) : ?>
      <li><strong>Catégorie :</strong> <?php echo esc_html( implode(', ', wp_list_pluck($categories, 'name')) ); ?></li>
    <?php endif; ?>

    <?php if ( $formats = get_the_terms(get_the_ID(), 'format') ) : ?>
      <li><strong>Format :</strong> <?php echo esc_html( implode(', ', wp_list_pluck($formats, 'name')) ); ?></li>
    <?php endif; ?>

    <?php if ( $type = get_post_meta(get_the_ID(), 'type', true) ) : ?>
      <li><strong>Type :</strong> <?php echo esc_html($type); ?></li>
    <?php endif; ?>

    <?php if ( $reference = get_post_meta(get_the_ID(), 'reference', true) ) : ?>
      <li><strong>Référence :</strong> <?php echo esc_html($reference); ?></li>
    <?php endif; ?>

    <li><strong>Date :</strong> <?php echo get_the_date(); ?></li>
  </ul>

</article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>