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
})(jQuery);
