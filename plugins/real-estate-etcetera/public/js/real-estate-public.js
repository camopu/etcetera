(function( $ ) {
	'use strict';

	$('#real-estate-filter').on('submit', function(e) {
    e.preventDefault();
    
    var filterData = $(this).serialize(); // Собираем данные формы

    $.ajax({
        url: ajaxurl, // URL для AJAX
        type: 'POST',
        data: filterData + '&action=real_estate_search', // Добавляем действие
        success: function(response) {
            $('#real-estate-results').html(response); // Обновляем результаты
        }
    });
});

// Обработка клика на номера страниц
	$(document).on('click', '.page-link', function(e) {
		e.preventDefault(); // Предотвращаем стандартное поведение ссылки

		var page = $(this).data('page'); // Получаем номер страницы
		var filterData = $('#real-estate-filter').serialize(); // Собираем данные формы

		$.ajax({
				url: ajaxurl, // URL для AJAX-запроса
				type: 'POST',
				data: filterData + '&action=real_estate_search&paged=' + page,
				success: function(response) {
						$('#real-estate-results').html(response); // Выводим результаты
				}
		});
	});

})( jQuery );