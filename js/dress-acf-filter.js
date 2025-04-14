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
			console.log({ $select, args, settings, field });

			categoriesFieldValue = $select.val();

			setTimeout(() => {
				// filterFieldsCheckboxType();
			}, 0);

			const changeHandler = (event) => {
				categoriesFieldValue = $select.val();

				// Отключаем обработчик перед изменением значения
				field.$el.off('change', changeHandler);

				// filterFieldValues('field_67d801f8498e7');
				// filterFieldValues('field_67d801c6498e4');

				// filterFieldsCheckboxType();

				// Включаем обработчик после изменения значения
				field.$el.on('change', changeHandler);
			};

			field.$el.on('change', changeHandler);
		}
	});

	// acf.add_filter("select2_ajax_results", function (json, params, instance) {
	//   if (!categoriesFieldValue) {
	//     return json;
	//   }

	//   if (instance.data.field.data.key === "field_67d801f8498e7") {
	//     const { map, dependencies } = dressData;

	//     /**
	//      * @type {Array}
	//      */
	//     const allowedColors = dependencies[map[categoriesFieldValue[0]]].color;

	//     if (!allowedColors) {
	//       return json;
	//     }

	//     json.results = json.results.filter(
	//       (colorObject) => allowedColors.indexOf(colorObject.id) !== -1
	//     );
	//   }
	//   // do something to json

	//   console.log({ json, params, instance });
	//   // return
	//   return json;
	// });

	acf.add_filter(
		'select2_ajax_data',
		function (data, args, $input, field, instance) {
			console.log({ data, args, $input, field, instance });

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

			data['include'] = allowedValues.join(',');
			data['number'] = 0;

			console.log({ data });

			return data;

			// if (instance.data.field.data.key === 'field_67d801f8498e7') {
			// 	let allowedColors = getAvailableFiltersByName('color');

			// 	console.log({ colors: allowedColors });

			// 	if (!allowedColors) {
			// 		return data;
			// 	}

			// 	data['include'] = allowedColors.join(',');
			// }

			// if (instance.data.field.data.key === 'field_67d801c6498e4') {
			// 	let allowedStyles = getAvailableFiltersByName('style');

			// 	console.log({ styles: allowedStyles });

			// 	if (!allowedStyles) {
			// 		return data;
			// 	}

			// 	data['include'] = allowedStyles.join(',');
			// }

			// if (instance.data.field.data.key === 'field_67d8023f498e9') {
			// 	let allowedBrands = getAvailableFiltersByName('brand');

			// 	console.log({ brands: allowedBrands });

			// 	if (!allowedBrands) {
			// 		return data;
			// 	}

			// 	data['include'] = allowedBrands.join(',');
			// }

			// if (instance.data.field.data.key === 'field_67d80188498e3') {
			// 	let allowedSilhouettes = getAvailableFiltersByName('silhouette');

			// 	console.log({ silhouettes: allowedSilhouettes });

			// 	if (!allowedSilhouettes) {
			// 		return data;
			// 	}

			// 	data['include'] = allowedSilhouettes.join(',');
			// }

			// // return
			// return data;
		}
	);

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

			if (dependencies[categoryId][filterName]) {
				awailableFilters = [
					...awailableFilters,
					...dependencies[categoryId][filterName],
				];
			}
		}

		return [...new Set(awailableFilters)];
	}
})(jQuery);
