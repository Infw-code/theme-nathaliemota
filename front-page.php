<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<?php get_header();
$args = array(
  'post_type'      => 'attachment',
  'post_mime_type' => 'image',
  'posts_per_page' => 1,
  'post_status'    => 'any',
  'orderby'        => 'date',
  'order'          => 'DESC'
);
$hero_image = get_posts($args);
?>


<!-- Hero -->
<div>
  <?php
  $hero_images = get_posts([
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => -1, // toutes les images
    'post_parent'    => 0   // ou un ID de post précis si c’est une galerie liée
  ]);

  if ($hero_images) {
    $random_key   = array_rand($hero_images);
    $random_image = $hero_images[$random_key]->guid;
  }
  ?>
  <div class="hero" style="background-image: url('<?php echo esc_url($random_image); ?>')"></div>
  <div class="hero-text">
    <h1>PHOTOGRAPHE EVENT</h1>
  </div>
</div>
<!-- FILTRES -->
<form id="filter-form" action="#">
  <div class="filter-bar">
    <div class="filterDouble">
      <!-- Catégorie -->
      <select name="categorie" class="categorie">
        <option value="" selected>CATÉGORIES</option>
        <?php
        $terms = get_terms(['taxonomy' => 'categorie', 'hide_empty' => false]);
        $current_cat = isset($_GET['categorie']) ? $_GET['categorie'] : '';
        foreach ($terms as $term) {
          $selected = ($current_cat === $term->slug) ? 'selected' : '';
          echo "<option value='{$term->slug}' {$selected}>{$term->name}</option>";
        }
        ?>
      </select>

      <!-- Format -->
      <select name="format" class="format">
        <option value="" selected>FORMATS</option>
        <?php
        $terms = get_terms(['taxonomy' => 'format', 'hide_empty' => false]);
        $current_format = isset($_GET['format']) ? $_GET['format'] : '';
        foreach ($terms as $term) {
          $selected = ($current_format === $term->slug) ? 'selected' : '';
          echo "<option value='{$term->slug}' {$selected}>{$term->name}</option>";
        }
        ?>
      </select>
    </div>

    <!-- Trier par -->
    <div class="trier-par">
      <select name="trier-par" class="trier-par-select">
        <option value="" selected>TRIER PAR</option>
        <option value="date_desc" <?php echo (isset($_GET['trier-par']) && $_GET['trier-par'] == 'date_desc') ? 'selected' : ''; ?>>+ RÉCENT</option>
        <option value="date_asc" <?php echo (isset($_GET['trier-par']) && $_GET['trier-par'] == 'date_asc') ? 'selected' : ''; ?>>- RÉCENT</option>
      </select>
    </div>
  </div>
</form>
<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".filter-bar select").forEach(function (selectEl) {
    const wrapper = document.createElement("div");
    wrapper.className = "custom-select";
    selectEl.style.display = "none";
    selectEl.parentNode.insertBefore(wrapper, selectEl);
    wrapper.appendChild(selectEl);

    const selected = document.createElement("div");
    selected.className = "selected";
    selected.textContent =
      selectEl.options[selectEl.selectedIndex]?.text ||
      selectEl.options[0]?.text;
    wrapper.appendChild(selected);

    const optionList = document.createElement("ul");
    optionList.className = "options";

    Array.from(selectEl.options).forEach((opt, index) => {
      // on ignore le placeholder si value vide
      if (opt.value === "") return;

      const li = document.createElement("li");
      li.textContent = opt.text;
      li.dataset.value = opt.value;

      // appliquer la classe rouge si c'est l'option sélectionnée
      if (opt.selected) {
        li.classList.add("selected-option");
      }

      li.addEventListener("click", () => {
        // reset
        optionList.querySelectorAll("li").forEach((el) =>
          el.classList.remove("selected-option")
        );
        li.classList.add("selected-option");

        selectEl.value = opt.value;
        selected.textContent = opt.text;
        optionList.classList.remove("open");
        selectEl.dispatchEvent(new Event("change"));
      });

      optionList.appendChild(li);
    });

    wrapper.appendChild(optionList);

    selected.addEventListener("click", () => {
      optionList.classList.toggle("open");
    });

    document.addEventListener("click", (e) => {
      if (!wrapper.contains(e.target)) {
        optionList.classList.remove("open");
      }
    });
  });
});
</script>
<!-- Galerie -->
<div class="gallery-grid" id="gallery">
  <?php
  $displayed_ids = [];

  $args = [
    'post_type'      => 'photo',
    'posts_per_page' => 8,
    'orderby'        => 'date', // ⚠️ Plus de rand()
    'order'          => 'DESC',
  ];
  $photos = get_posts($args);

  foreach ($photos as $photo) {
    $displayed_ids[] = $photo->ID;
    $titre = get_the_title($photo->ID);
    $categories = get_the_terms($photo->ID, 'categorie');
    $categorie_nom = $categories && !is_wp_error($categories) ? $categories[0]->name : '';

    echo '<div class="gallery-item">';
    echo '<a href="' . get_permalink($photo->ID) . '" data-id="' . $photo->ID . '">';
    echo get_the_post_thumbnail($photo->ID, 'medium_large');
    echo '</a>';

    echo '<div class="gallery-overlay">';
    echo '<div class="overlay-eye">';
    echo '<a href="' . get_permalink($photo->ID) . '">';
    echo '<img src="' . get_template_directory_uri() . '/images/Group.png" alt="Voir la photo" class="eye-icon-img">';
    echo '</a>';
    echo '</div>';

    echo '<div class="overlay-action">';
    echo '<span class="open-lightbox" data-image="' . esc_url(wp_get_attachment_url(get_post_thumbnail_id($photo->ID))) . '" data-caption="' . esc_attr($titre) . '">';
    echo '<img src="' . get_template_directory_uri() . '/images/Icon_fullscreen.png" alt="Voir en plein écran" class="fullscreen-icon">';
    echo '</span>';
    echo '</div>';

    echo '<div class="overlay-text">';
    echo '<span class="photo-title">' . esc_html($titre) . '</span>';
    echo '<span class="photo-cat">' . esc_html($categorie_nom) . '</span>';
    echo '</div>';
    echo '</div>'; // .gallery-overlay
    echo '</div>'; // .gallery-item
  }
  ?>
</div>

<button id="loadMore">Charger plus</button>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const gallery = document.getElementById('gallery');
    const loadMoreBtn = document.getElementById('loadMore');
    const filterForm = document.getElementById('filter-form');

    let offset = 0;
    let loading = false;
    let exclude = [];

    function loadMore(reset = false) {
      if (loading) return;
      loading = true;

      if (reset) {
        gallery.innerHTML = '';
        offset = 0;
        exclude = [];
      }

      const formData = new FormData(filterForm);
      let params = new URLSearchParams();
      formData.forEach((value, key) => {
        if (value) params.append(key, value);
      });
      params.append('offset', offset);
      params.append('action', 'load_more_images');
      params.append('exclude', exclude.join(','));

      fetch(`<?php echo admin_url('admin-ajax.php'); ?>?${params.toString()}`)
        .then(res => res.text())
        .then(data => {
          gallery.insertAdjacentHTML('beforeend', data);

          gallery.querySelectorAll('.gallery-item a[data-id]').forEach(a => {
            const id = parseInt(a.dataset.id);
            if (!exclude.includes(id)) exclude.push(id);
          });

          offset = exclude.length;

          if (data.includes('END_OF_GALLERY')) loadMoreBtn.style.display = 'none';
          else loadMoreBtn.style.display = 'block';

          loading = false;
        })
        .catch(err => {
          console.error(err);
          loading = false;
        });
    }

  })
</script>

<?php get_footer(); ?>