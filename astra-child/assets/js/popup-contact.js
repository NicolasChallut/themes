document.addEventListener('DOMContentLoaded', function() {
    var contactLinks = document.querySelectorAll('.contact-link');
    var popup = document.querySelector('.popup-overlay');
    var closeBtn = document.querySelector('.popup-close');
    var photoRefInput = document.querySelector('#photo-ref');

    // Vérifiez que la variable est définie avant de l'utiliser
    if (typeof myPopupData !== 'undefined') {
        window.photoReference = myPopupData.photoReference;
    }

    function openPopup() {
        popup.style.display = 'block';
    }

    function closePopup() {
        popup.style.display = 'none';
    }

    contactLinks.forEach(function(contactLink) {
        contactLink.addEventListener('click', function(event) {
            event.preventDefault();
            if (photoRefInput) {
                photoRefInput.value = window.photoReference || ''; // Valeur par défaut si non définie
            }
            openPopup();
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closePopup();
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            closePopup();
        }
    });
});
