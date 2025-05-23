const initDressSorting = () => {
	(function ($) {
		'use strict';

		$(document).ready(function () {
			document.head.insertAdjacentHTML(
				'beforeend',
				`<style>
          .ui-state-highlight {background-color: pink !important;} 
        </style>`
			);
			const $table = $('.wp-list-table');
			const $tbody = $table.find('tbody');
			const postsPerPage = parseInt($('#edit_dress_per_page').val()) || 10;

			$tbody.sortable({
				placeholder: 'ui-state-highlight',
				classes: {
					'ui-sortable': 'sortable',
					'ui-sortable-handle': 'sortable__handle',
					'ui-sortable-helper': 'sortable__helper',
				},
				handle: '.column-menu_order',
				axis: 'y',
				items: '> tr',
				helper: fixHelper,
				update: function (event, ui) {
					const order = [];
					$tbody.find('tr').each(function () {
						order.push($(this).attr('id').replace('post-', ''));
					});

					const page =
						parseInt(
							new URLSearchParams(window.location.search).get('paged')
						) || 1;
					const dressCategorySlug = new URLSearchParams(
						window.location.search
					).get('dress_category');

					$.ajax({
						url: LOVE_FOREVER_ADMIN.AJAX_URL,
						type: 'POST',
						data: {
							action: 'update_dress_order',
							order: order,
							page: page,
							posts_per_page: postsPerPage,
							dress_category: dressCategorySlug,
							nonce: LOVE_FOREVER_ADMIN.NONCE,
						},
						success: function (response) {
							if (!response.success) {
								console.error(response.data.debug);
								alert(response.data.message);
								return;
							}

							// Обновляем отображаемые номера
							updateDisplayOrder(response.data.result);
						},
						error: function () {
							alert('Произошла ошибка при обновлении порядка.');
							window.location.reload();
						},
					});
				},
				start: function (e, ui) {
					const $targetElement = $(ui.item);
					const $placeholder = $(ui.placeholder);

					$placeholder
						.find('td')
						.attr('colspan', $targetElement.find('th:not(.hidden),td:not(.hidden)').length);
				},
				stop: function (e, ui) {
					$tbody.css('width', '');
				},
			});
			$tbody.disableSelection();

			function updateDisplayOrder(orderData) {
				for (const index in orderData) {
					if (Object.prototype.hasOwnProperty.call(orderData, index)) {
						const postId = orderData[index];
						$(`#post-${postId} .column-menu_order`).text(index);
					}
				}
			}
		});

		function fixHelper(e, ui) {
			ui.children().each(function () {
				$(this).width($(this).width());
			});
			return ui;
		}
	})(jQuery);
};

export default initDressSorting;
