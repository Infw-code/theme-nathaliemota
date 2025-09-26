<footer>
  <div class="footer-content">
    <p>MENTIONS LÉGALES</p>
    <p>VIE PRIVÉE</p>
    <p>TOUS DROITS RÉSERVÉS</p>
  </div>
</footer>

<!-- Lightbox : doit être hors footer -->
<div id="lightbox" class="lightbox hidden">
  <div class="lightbox-overlay"></div>
  <div class="lightbox-content">
    <button id="close-lightbox" class="lightbox-close">&times;</button>
    <img id="lightbox-image" src="" alt="">
    
    <!-- Zone infos : référence à gauche, catégorie à droite -->
    <div class="lightbox-info">
      <p class="lightbox-reference"></p>
      <p class="lightbox-category"></p>
    </div>

    <!-- Boutons navigation -->
    <button id="prev-photo" class="nav-arrow prev">← Précédent</button>
    <button id="next-photo" class="nav-arrow next">Suivant →</button>
  </div>
</div>

<!-- Modale contact -->
<?php get_template_part('template_parts/modal-contact'); ?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("modal-contact"); // ton ID réel

    document.querySelectorAll(".open-modal").forEach(button => {
      button.addEventListener("click", () => {
        const ref = button.getAttribute("data-reference") || "";

        // champ de référence dans le CF7
        const referenceField = modal ? modal.querySelector("#reference") : null;

        if (referenceField) {
          referenceField.value = ref;
          console.log("[DEBUG] référence injectée :", ref);
        } else {
          console.warn("[DEBUG] champ #reference introuvable");
        }

        // ouverture de la modale
        if (modal) {
          modal.classList.add("active");
          modal.setAttribute("aria-hidden", "false");
        }
      });
    });

    // fermeture via le bouton ×
    const closeBtn = modal ? modal.querySelector(".close-button") : null;
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        modal.classList.remove("active");
        modal.setAttribute("aria-hidden", "true");
      });
    }
  });
</script>

<?php wp_footer(); ?>
</body>

</html>