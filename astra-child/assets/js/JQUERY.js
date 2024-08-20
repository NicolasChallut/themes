jQuery(document).ready(function($) {
    $('#filter-form').on('change', function() {
        var selectedCategory = $('#category-filter').val().toLowerCase();
        var selectedFormat = $('#format-filter').val().toLowerCase();
        var selectedYear = $('#year-filter').val();

        $('.gallery-item').each(function() {
            var itemCategory = $(this).data('category');
            var itemFormat = $(this).data('format');
            var itemYear = $(this).data('year');

            if ((selectedCategory === '' || itemCategory === selectedCategory) &&
                (selectedFormat === '' || itemFormat === selectedFormat) &&
                (selectedYear === '' || itemYear == selectedYear)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
