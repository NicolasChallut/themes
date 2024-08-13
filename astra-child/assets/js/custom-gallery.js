document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('filter-category');
    const yearFilter = document.getElementById('filter-year');
    const typeFilter = document.getElementById('filter-type');
    const tableRows = document.querySelectorAll('#photo-table tbody tr');

    function filterPhotos() {
        const category = categoryFilter.value;
        const year = yearFilter.value;
        const type = typeFilter.value;

        tableRows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');
            const rowYear = row.getAttribute('data-year');
            const rowType = row.getAttribute('data-type');

            const categoryMatch = (category === 'all' || rowCategory === category);
            const yearMatch = (year === 'all' || rowYear === year);
            const typeMatch = (type === 'all' || rowType === type);

            if (categoryMatch && yearMatch && typeMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterPhotos);
    yearFilter.addEventListener('change', filterPhotos);
    typeFilter.addEventListener('change', filterPhotos);
});