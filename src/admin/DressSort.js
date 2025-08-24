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

			const postType =
				new URLSearchParams(window.location.search).get('post_type') || 'post';
			const postTypesMap = {
				dress: 'update_dress_order',
				promo_blocks: 'update_promo_order',
				story: 'update_story_order',
			};

			if (!postTypesMap[postType]) {
				return;
			}

			const postsPerPageInputId = `edit_${postType}_per_page`;
			const postsPerPage = parseInt($(`#${postsPerPageInputId}`).val()) || 10;

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

					const actionName = postTypesMap[postType];

					const urlParams = new URLSearchParams(window.location.search);
					const ajaxData = {
						action: actionName,
						order: order,
						page: page,
						posts_per_page: postsPerPage,
						nonce: LOVE_FOREVER_ADMIN.NONCE,
					};
					// Добавляем все GET параметры из URL (не перезаписываем уже существующие в ajaxData)
					for (const [key, value] of urlParams.entries()) {
						if (!(key in ajaxData)) {
							ajaxData[key] = value;
						}
					}

					$.ajax({
						url: LOVE_FOREVER_ADMIN.AJAX_URL,
						type: 'POST',
						data: ajaxData,
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

					const $td = $placeholder.find('td');

					$td.each((index, td) => {
						if (index > 0) {
							td.remove();
						}
					});

					$td.attr(
						'colspan',
						$targetElement.find('th:not(.hidden),td:not(.hidden)').length
					);
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
