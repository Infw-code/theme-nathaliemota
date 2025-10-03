<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>
<body <?php body_class(); ?>>
<header>
    <div class="logo">
        <a href="<?php echo home_url(); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Mon Logo">
        </a>
    </div>

    <!-- Bouton hamburger -->
    <button class="menu-toggle">&#9776;</button>

    <nav class="menu-right">
        <ul>
            <li><a href="<?php echo home_url(); ?>#accueil">ACCUEIL</a></li>
            <li><a href="<?php echo home_url(); ?>/a-propos">À PROPOS</a></li>
            <li><a href="#" class="open-modal-contact menu-contact-link">CONTACT</a></li>
        </ul>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu-right');

    menuToggle.addEventListener('click', function() {
        menu.classList.toggle('active');
    });
});
</script>