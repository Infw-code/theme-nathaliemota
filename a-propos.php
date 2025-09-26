<?php
/*
Template Name: À Propos
*/
?>
<?php get_header(); ?>

<div class="page-content">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content(); // Affiche le contenu que tu modifies dans l'éditeur WordPress
        endwhile;
    endif;
    ?>
</div>

<?php get_footer(); ?>