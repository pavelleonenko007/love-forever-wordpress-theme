(function ($) {
	let categoriesFieldValue = null;
	const postForm = $('#post');

	// acf.addAction('ready', () => {
	// 	filterFieldsCheckboxType();

	// 	postForm.on('change', () => {
	// 		setTimeout(() => {
	// 			filterFieldsCheckboxType();
	// 		}, 200);
	// 	});
	// });

	// Обрабатываем динамическую загрузку контента ACF
	acf.addAction('append', function ($el) {
		filterFieldsCheckboxType();
	});

	// Обрабатываем загрузку новых полей
	acf.addAction('load', function () {
		filterFieldsCheckboxType();
	});

	acf.addAction('select2_init', ($select, args, settings, field) => {
		if (field.data.key === 'field_67d6fec761d73') {
			// console.log({ $select, args, settings, field });

			categoriesFieldValue = $select.val();

			setTimeout(() => {
				// filterFieldsCheckboxType();
				initQuickLinks(field);
			}, 0);

			const changeHandler = (event) => {
				categoriesFieldValue = $select.val();

				console.log({ categoriesFieldValue });
			};

			field.$el.on('change', changeHandler);
		}
	});

	acf.add_filter(
		'select2_ajax_data',
		function (data, args, $input, field, instance) {
			console.log('select2_ajax_data', { data, args, $input, field, instance });

			if (categoriesFieldValue) {
				data['dress_id'] = categoriesFieldValue;
			}

			return data;

			// do something to data

			const map = {
				field_67d801f8498e7: 'color',
				field_67d8023f498e9: 'brand',
				field_67d80188498e3: 'silhouette',
				field_67d801c6498e4: 'style',
				field_67d801dc498e5: 'fabric',
				field_67d8020a498e8: 'dress_tag',
			};

			const fieldName = map[instance.data.field.data.key];

			if (!fieldName) {
				return data;
			}

			const allowedValues = getAvailableFiltersByName(fieldName);

			if (!allowedValues) {
				return data;
			}

			// data['custom_filtered'] = '1';
			data['include'] = allowedValues.join(',');
			// data['per_page'] = 100;
			// data['number'] = allowedValues.length;
			// data['paged'] = 1;

			console.log({ data });

			return data;
		}
	);

	acf.add_filter('select2_ajax_results', function (json, params, instance) {
		// do something to json

		console.log('select2_ajax_results', { json, params, instance });

		// json.limit = 100;
		// json.more = false;
		// json.pagination = {
		// 	more: false,
		// };

		// return
		return json;
	});

	function filterFieldsCheckboxType() {
		console.log('filterFieldsCheckboxType');

		const fieldsWithCheckboxType = {
			style: 'field_67d801c6498e4',
			fabric: 'field_67d801dc498e5',
		};

		for (const name in fieldsWithCheckboxType) {
			const key = fieldsWithCheckboxType[name];

			const field = acf.getField(key);
			const allFieldItems = field.$el.find('li[data-id]');
			const allowedValues = getAvailableFiltersByName(name);

			console.log({ name, allowedValues });

			if (!categoriesFieldValue || allowedValues.length === 0) {
				allFieldItems.each((_, item) => {
					item.querySelector('input').disabled = false;
				});

				return;
			}

			allFieldItems.each((_, item) => {
				const { id } = item.dataset;
				item.querySelector('input').disabled = !allowedValues.includes(
					parseInt(id)
				);
			});
		}
	}

	/**
	 *
	 * @param {string} filterName
	 * @returns {Array}
	 */
	function getAvailableFiltersByName(filterName) {
		if (!categoriesFieldValue) {
			return [];
		}

		const { map, dependencies } = dressData;

		let awailableFilters = [];

		for (let index = 0; index < categoriesFieldValue.length; index++) {
			const categoryId = categoriesFieldValue[index];

			if (dependencies[categoryId] && dependencies[categoryId][filterName]) {
				awailableFilters = [
					...awailableFilters,
					...dependencies[categoryId][filterName],
				];
			}
		}

		return [...new Set(awailableFilters)];
	}

	/**
	 * Инициализация быстрых ссылок для поля категорий
	 * @param {Object} field - ACF поле
	 */
	function initQuickLinks(field) {
		const $fieldEl = field.$el;
		const $select = $fieldEl.find('select');
		
		// Проверяем, не добавлены ли уже быстрые ссылки
		if ($fieldEl.find('.quick-links-container').length > 0) {
			return;
		}

		// Получаем структурированные категории из PHP
		const categorizedCategories = window.LOVE_FOREVER_QUICK_LINKS?.categories || {};

		// Создаем контейнер для быстрых ссылок
		const $quickLinksContainer = $('<div class="quick-links-container"></div>');
		const $quickLinksTitle = $('<div class="quick-links-title">Быстрый выбор:</div>');
		const $quickLinksWrapper = $('<div class="quick-links-wrapper"></div>');

		// Создаем колонки для каждой категории
		Object.keys(categorizedCategories).forEach(categoryName => {
			const categoryData = categorizedCategories[categoryName];
			
			// Создаем колонку
			const $column = $('<div class="quick-links-column"></div>');
			
			// Заголовок колонки
			const $columnHeader = $(`
				<div class="quick-links-column-header">
					<span class="column-icon">${categoryData.icon}</span>
					<span class="column-title">${categoryName}</span>
				</div>
			`);
			
			// Контейнер для кнопок в колонке
			const $columnButtons = $('<div class="quick-links-column-buttons"></div>');
			
			// Создаем кнопки для каждой категории в колонке
			categoryData.items.forEach(item => {
				const $button = $(`
					<button type="button" class="quick-link-btn" data-category-id="${item.id}" data-category-name="${item.name}" style="background-color: ${categoryData.color}">
						<span class="quick-link-icon">${item.icon}</span>
						<span class="quick-link-text">${item.name}</span>
					</button>
				`);

				$button.on('click', function() {
					const $btn = $(this);
					
					// Показываем индикатор загрузки
					$btn.addClass('loading');
					$btn.prop('disabled', true);
					
					// Определяем, нужно ли добавлять приписку
					const displayName = categoryName === 'Распродажа' ? `${item.name} (Распродажа)` : item.name;
					
					// Создаем массив категорий для добавления
					const categoriesToAdd = [
						{ id: item.id, name: displayName }
					];
					
					// Добавляем дополнительные категории, если они есть
					if (item.additional_categories && item.additional_categories.length > 0) {
						item.additional_categories.forEach(additionalCategory => {
							// Для дополнительных категорий не добавляем приписку
							categoriesToAdd.push({
								id: additionalCategory.id,
								name: additionalCategory.name
							});
						});
					}
					
					// Добавляем все категории
					addMultipleCategories($select, categoriesToAdd, function() {
						// Убираем индикатор загрузки
						$btn.removeClass('loading');
						$btn.prop('disabled', false);
					});
				});

				$columnButtons.append($button);
			});
			
			$column.append($columnHeader);
			$column.append($columnButtons);
			$quickLinksWrapper.append($column);
		});

		$quickLinksContainer.append($quickLinksTitle);
		$quickLinksContainer.append($quickLinksWrapper);

		// Вставляем быстрые ссылки перед полем
		$fieldEl.prepend($quickLinksContainer);
	}

	/**
	 * Добавляет несколько категорий в поле
	 * @param {jQuery} $select - Select2 элемент
	 * @param {Array} categories - Массив категорий с id и name
	 * @param {Function} callback - Функция обратного вызова
	 */
	function addMultipleCategories($select, categories, callback) {
		let completed = 0;
		const total = categories.length;
		
		if (total === 0) {
			if (callback) callback();
			return;
		}
		
		categories.forEach((category, index) => {
			setTimeout(() => {
				selectCategoryById($select, category.id, category.name, function() {
					completed++;
					if (completed === total && callback) {
						callback();
					}
				});
			}, index * 100); // Небольшая задержка между добавлениями
		});
	}

	/**
	 * Выбирает категорию по ID (мгновенный способ)
	 * @param {jQuery} $select - Select2 элемент
	 * @param {number} categoryId - ID категории
	 * @param {string} categoryName - Название категории
	 * @param {Function} callback - Функция обратного вызова
	 */
	function selectCategoryById($select, categoryId, categoryName, callback) {
		// Проверяем, есть ли уже опция с таким ID
		const existingOption = $select.find(`option[value="${categoryId}"]`);
		
		if (existingOption.length > 0) {
			// Если опция уже существует, выбираем её
			selectCategoryDirectly($select, existingOption);
		} else {
			// Если опции нет, создаем новую
			const $newOption = $(`<option value="${categoryId}">${categoryName}</option>`);
			$select.append($newOption);
			
			// Выбираем категорию
			selectCategoryDirectly($select, $newOption);
		}
		
		if (callback) callback();
	}

	/**
	 * Выбирает категорию через AJAX запрос (быстрый способ)
	 * @param {jQuery} $select - Select2 элемент
	 * @param {string} searchTerm - Поисковый термин
	 * @param {Function} callback - Функция обратного вызова
	 */
	function selectCategoryByAjax($select, searchTerm, callback) {
		// Сначала пытаемся найти категорию в уже загруженных опциях
		const foundOption = findCategoryInOptions($select, searchTerm);
		if (foundOption) {
			selectCategoryDirectly($select, foundOption);
			if (callback) callback();
			return;
		}

		// Если не найдено, получаем ID через AJAX
		getCategoryIdByName(searchTerm, function(categoryData) {
			if (categoryData) {
				// Добавляем новую опцию в select
				const $newOption = $(`<option value="${categoryData.id}">${categoryData.name}</option>`);
				$select.append($newOption);
				
				// Выбираем категорию
				selectCategoryDirectly($select, $newOption);
			} else {
				// Если AJAX не сработал, используем старый способ
				searchCategoryViaAjax($select, searchTerm);
			}
			
			if (callback) callback();
		});
	}

	/**
	 * Получает ID категории по названию через AJAX
	 * @param {string} categoryName - Название категории
	 * @param {Function} callback - Функция обратного вызова
	 */
	function getCategoryIdByName(categoryName, callback) {
		$.ajax({
			url: window.LOVE_FOREVER_ADMIN?.AJAX_URL || '/wp-admin/admin-ajax.php',
			type: 'POST',
			data: {
				action: 'get_category_id_by_name',
				category_name: categoryName,
				taxonomy: 'dress_category',
				nonce: window.LOVE_FOREVER_ADMIN?.NONCE || ''
			},
			success: function(response) {
				if (response.success) {
					callback(response.data);
				} else {
					callback(null);
				}
			},
			error: function() {
				callback(null);
			}
		});
	}

	/**
	 * Выбирает категорию через поиск в Select2 (старый способ)
	 * @param {jQuery} $select - Select2 элемент
	 * @param {string} searchTerm - Поисковый термин
	 */
	function selectCategoryBySearch($select, searchTerm) {
		// Сначала пытаемся найти категорию в уже загруженных опциях
		const foundOption = findCategoryInOptions($select, searchTerm);
		if (foundOption) {
			selectCategoryDirectly($select, foundOption);
			return;
		}

		// Если не найдено, используем AJAX поиск
		searchCategoryViaAjax($select, searchTerm);
	}

	/**
	 * Ищет категорию в уже загруженных опциях
	 * @param {jQuery} $select - Select2 элемент
	 * @param {string} searchTerm - Поисковый термин
	 * @returns {Object|null} - Найденная опция или null
	 */
	function findCategoryInOptions($select, searchTerm) {
		const $options = $select.find('option');
		let foundOption = null;

		$options.each(function() {
			const optionText = $(this).text().trim();
			// Ищем точное совпадение или совпадение в начале названия
			if (optionText === searchTerm || optionText.startsWith(searchTerm)) {
				foundOption = $(this);
				return false; // Прерываем цикл
			}
		});

		// Если не найдено точного совпадения, ищем частичное
		if (!foundOption) {
			$options.each(function() {
				const optionText = $(this).text().trim();
				if (optionText.includes(searchTerm)) {
					foundOption = $(this);
					return false; // Прерываем цикл
				}
			});
		}

		return foundOption;
	}

	/**
	 * Выбирает категорию напрямую без поиска
	 * @param {jQuery} $select - Select2 элемент
	 * @param {jQuery} $option - Опция для выбора
	 */
	function selectCategoryDirectly($select, $option) {
		const optionValue = $option.val();
		const currentValues = $select.val() || [];
		
		// Проверяем, не выбрана ли уже эта категория
		if (!currentValues.includes(optionValue)) {
			currentValues.push(optionValue);
			$select.val(currentValues).trigger('change');
		}
	}

	/**
	 * Ищет категорию через AJAX поиск
	 * @param {jQuery} $select - Select2 элемент
	 * @param {string} searchTerm - Поисковый термин
	 */
	function searchCategoryViaAjax($select, searchTerm) {
		// Открываем Select2
		$select.select2('open');
		
		// Находим конкретное поле поиска для этого Select2
		const select2Container = $select.next('.select2-container');
		const $searchInput = select2Container.find('.select2-search__field');
		
		// Очищаем поиск и вводим поисковый термин
		$searchInput.val(searchTerm).trigger('input');
		
		// Ждем загрузки результатов и выбираем первый подходящий
		setTimeout(() => {
			const $results = select2Container.find('.select2-results__option:not(.select2-results__option--highlighted)');
			let found = false;
			
			$results.each(function() {
				const resultText = $(this).text().trim();
				// Ищем точное совпадение или совпадение в начале названия
				if (resultText === searchTerm || resultText.startsWith(searchTerm)) {
					$(this).trigger('mouseup');
					found = true;
					return false; // Прерываем цикл
				}
			});
			
			// Если не найдено точного совпадения, ищем частичное
			if (!found) {
				$results.each(function() {
					const resultText = $(this).text().trim();
					if (resultText.includes(searchTerm)) {
						$(this).trigger('mouseup');
						return false; // Прерываем цикл
					}
				});
			}
		}, 800); // Увеличиваем время ожидания для AJAX запроса
	}
})(jQuery);
