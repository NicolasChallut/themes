document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const photoGallery = document.getElementById('photo-gallery');

    function fetchFilteredImages() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();

        fetch(`/wp-json/my-plugin/v1/filtered-images?${params}`)
            .then(response => response.json())
            .then(data => {
                photoGallery.innerHTML = ''; // Vider la galerie avant de la remplir
                data.forEach(image => {
                    const imgElement = document.createElement('img');
                    imgElement.src = image.url;
                    imgElement.alt = image.title;
                    const photoCard = document.createElement('div');
                    photoCard.className = 'photo-card';
                    photoCard.appendChild(imgElement);
                    photoGallery.appendChild(photoCard);
                });
            })
            .catch(error => console.error('Error fetching images:', error));
    }

    filterForm.addEventListener('change', fetchFilteredImages);
    fetchFilteredImages(); // Charger les images initiales
});
