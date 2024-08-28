document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les éléments ayant la classe 'contact-trigger'
    var contactLinks = document.querySelectorAll('.contact-link'); // Utilisation de querySelectorAll pour sélectionner plusieurs éléments
    var popup = document.querySelector('.popup-overlay');
    var closeBtn = document.querySelector('.popup-close');

    // Fonction pour ouvrir le popup
    function openPopup() {
        popup.style.display = 'block';
    }

    // Fonction pour fermer le popup
    function closePopup() {
        popup.style.display = 'none';
    }

    // Ouvrir le popup au chargement de la page
    openPopup();

    // Ajouter des événements de clic à tous les éléments de contact
    contactLinks.forEach(function(contactLink) {
        contactLink.addEventListener('click', function(event) {
            event.preventDefault(); // Empêcher le comportement par défaut du lien
            openPopup();
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closePopup();
        });
    }

    // Fermer le popup si l'utilisateur clique en dehors de la zone de contenu
    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            closePopup();
        }
    });
});

