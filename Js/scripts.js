document.addEventListener("DOMContentLoaded", function() {
    console.log("JS principal chargé !");

    /* ======== MENU MOBILE ======== */
(function() {
    // On attend que le DOM soit prêt
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const menuRight = document.querySelector('.menu-right');

        if (!menuToggle || !menuRight) return;

        // On utilise stopPropagation pour éviter les conflits
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            menuRight.classList.toggle('active');
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function(e) {
            if (!menuRight.contains(e.target) && !menuToggle.contains(e.target)) {
                menuRight.classList.remove('active');
            }
        });
    });
})();
// ----- MODALE CONTACT (single-photo) -----
const modalContact = document.getElementById("modal-contact");
// Tous les boutons qui ouvrent la modale contact
const openModalContactBtns = document.querySelectorAll(".open-modal-contact, .open-modal-single");

if (modalContact && openModalContactBtns.length > 0) {
    const closeModalBtn = modalContact.querySelector(".close-button");

    openModalContactBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            modalContact.classList.add("active"); // affiche la modale
        });
    });

    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", () => modalContact.classList.remove("active"));
    }

    window.addEventListener("click", (e) => {
        if (e.target === modalContact) modalContact.classList.remove("active");
    });
}

// ----- MODALE FRONT-PAGE (header) -----
const modalFront = document.getElementById("modal-front");
// Tous les boutons qui ouvrent la modale front-page
const openModalFrontBtns = document.querySelectorAll(".open-modal-front");

if (modalFront && openModalFrontBtns.length > 0) {
    const closeFrontBtn = modalFront.querySelector(".close-button");

    openModalFrontBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            modalFront.classList.add("active"); // affiche la modale
        });
    });

    if (closeFrontBtn) {
        closeFrontBtn.addEventListener("click", () => modalFront.classList.remove("active"));
    }

    window.addEventListener("click", (e) => {
        if (e.target === modalFront) modalFront.classList.remove("active");
    });
}
    /* ======== GALERIE : FILTRES + LOAD MORE ======== */
    const gallery = document.getElementById("gallery");
    const loadMoreBtn = document.getElementById("loadMore");
    const filterForm = document.getElementById("filter-form");

    if (!gallery) return;

    let offset = 0;
    let loading = false;
    let exclude = [];

    function loadMore(reset = false) {
        if (loading) return;
        loading = true;

        if (reset) {
            gallery.innerHTML = "";
            offset = 0;
            exclude = [];
        }

        const formData = new FormData(filterForm);
        let params = new URLSearchParams();
        formData.forEach((value, key) => { if(value) params.append(key,value); });
        params.append("offset", offset);
        params.append("action", "load_more_images");
        params.append("exclude", exclude.join(","));

        fetch(`${themeVars.ajaxUrl}?${params.toString()}`)
            .then(res => res.text())
            .then(data => {
                // ⚡ Vérifier le marqueur END_OF_GALLERY et le supprimer
                const endReached = data.includes("END_OF_GALLERY");
                data = data.replace("<!-- END_OF_GALLERY -->", "");

                gallery.insertAdjacentHTML("beforeend", data);

                // mettre à jour exclude avec tous les IDs actuels
                gallery.querySelectorAll(".gallery-item a[data-id]").forEach(a => {
                    const id = parseInt(a.dataset.id);
                    if (!exclude.includes(id)) exclude.push(id);
                });

                offset = gallery.querySelectorAll(".gallery-item").length;

                // Gestion du bouton
                if (endReached || data.trim() === "") {
                    loadMoreBtn.style.display = "none";
                } else {
                    loadMoreBtn.style.display = "block";
                }

                document.dispatchEvent(new CustomEvent('galleryUpdated'));

                loading = false;
            })
            .catch(err => { console.error(err); loading=false; });
    }
    // charger les premières images
    loadMore(true);

    if (loadMoreBtn) loadMoreBtn.addEventListener("click", e => { e.preventDefault(); loadMore(false); });
    if (filterForm) filterForm.querySelectorAll("select").forEach(select => {
        select.addEventListener("change", () => loadMore(true));
    });
    
});