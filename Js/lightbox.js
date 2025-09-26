document.addEventListener("DOMContentLoaded", function() {
    const lightbox = document.getElementById("lightbox");
    const lightboxImage = document.getElementById("lightbox-image");
    const closeBtn = document.getElementById("close-lightbox");
    const overlay = document.querySelector(".lightbox-overlay");
    const prevBtn = document.getElementById("prev-photo");
    const nextBtn = document.getElementById("next-photo");

    const refEl = document.createElement("p");
    refEl.className = "lightbox-reference";
    const catEl = document.createElement("p");
    catEl.className = "lightbox-category";

    const captionWrapper = document.createElement("div");
    captionWrapper.className = "lightbox-info";
    captionWrapper.appendChild(refEl);
    captionWrapper.appendChild(catEl);
    lightbox.querySelector(".lightbox-content").appendChild(captionWrapper);

    let images = [];
    let currentIndex = 0;

    function initLightbox() {
        images = [];
        document.querySelectorAll(".open-lightbox").forEach((btn, index) => {
            images.push({ src: btn.dataset.image, caption: btn.dataset.caption });
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                currentIndex = index;
                showImage(currentIndex);
            });
        });
        console.log("[Lightbox] Initialisée avec", images.length, "images");
    }

    function showImage(index) {
        if (!lightbox || !lightboxImage) return;
        lightboxImage.src = images[index].src;

        // Séparer la référence et la catégorie (format attendu "reference | categorie")
        const [reference, categorie] = images[index].caption.split('|').map(s => s.trim());
        refEl.textContent = reference || '';
        catEl.textContent = categorie || '';

        lightbox.classList.remove("hidden");
    }

    if (prevBtn) prevBtn.addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showImage(currentIndex);
    });

    if (nextBtn) nextBtn.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % images.length;
        showImage(currentIndex);
    });

    if (closeBtn) closeBtn.addEventListener("click", () => lightbox.classList.add("hidden"));
    if (overlay) overlay.addEventListener("click", () => lightbox.classList.add("hidden"));

    document.addEventListener("keydown", (e) => {
        if (!lightbox || lightbox.classList.contains("hidden")) return;
        if (e.key === "Escape") lightbox.classList.add("hidden");
        if (e.key === "ArrowLeft" && prevBtn) prevBtn.click();
        if (e.key === "ArrowRight" && nextBtn) nextBtn.click();
    });

    // Initialisation au premier chargement
    initLightbox();

    // ⚡ Réagir quand la galerie est mise à jour par scripts.js
    document.addEventListener("galleryUpdated", initLightbox);
});