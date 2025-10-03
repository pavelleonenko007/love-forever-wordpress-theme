import { debounce, formDataToObject, scrollToElement, wait } from './utils';

const ROOT_SELECTOR = '[data-js-product-filter-form]';

// TODO: при фильрации сбрасываются фильтры, которые уже были в параметрах URL, если их нет в форме
class ProductFilterForm {
	selectors = {
		root: ROOT_SELECTOR,
		contentElement: '[data-js-product-filter-form-content-element]',
		paginationElement: '[data-js-product-filter-form-pagination]',
		paginationLinkElement: '[data-js-product-filter-form-paginate-link]',
		resetButton: '[data-js-product-filter-form-reset-button]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(element) {
		this.filterForm = element;
		this.contentElement = document.querySelector(this.selectors.contentElement);
		this.paginationElement = document.querySelector(
			this.selectors.paginationElement
		);

		this.defaultValues = {
			silhouette: '',
			color: [],
			brand: [],
			style: [],
			orderby: 'views',
			'min-price': this.filterForm.querySelector('[name="min-price"]').min,
			'max-price': this.filterForm.querySelector('[name="max-price"]').max,
		};

		this.onChange = this.onChange.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
		this.debouncedOnChange = debounce(this.onChange, 200);
		this.changePaginationLink = this.changePaginationLink.bind(this);
		this.resetForm = this.resetForm.bind(this);

		this.bindEvents();
	}

	updateResetButtonVisibility() {
		const hasActiveFilters = this.hasActiveFilters();

		document.querySelectorAll(this.selectors.resetButton).forEach((button) => {
			button.disabled = !hasActiveFilters;
		});
	}

	/**
	 * Проверяет, есть ли активные фильтры
	 * @returns {boolean}
	 */
	hasActiveFilters() {
		const formData = formDataToObject(new FormData(this.filterForm));

		for (const key in this.defaultValues) {
			if (Array.isArray(formData[key]) && formData[key].length > 0) {
				return true;
			}

			if (
				formData[key] !== undefined &&
				formData[key] !== this.defaultValues[key]
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 *
	 * @param {FormData} formData
	 */
	updateQueryParams(formData) {
		[
			'action',
			'submit_filter_form_nonce',
			'taxonomy',
			'dress_category',
		].forEach((key) => {
			formData.delete(key);
		});

		const params = new URLSearchParams();

		for (const [key, value] of formData.entries()) {
			params.append(key, value);
		}

		const paramsString = params.toString();

		window.history.replaceState(
			{},
			'',
			`${window.location.pathname}?${paramsString}`
		);
	}

	changePaginationLink(event) {
		const { target } = event;

		if (!target.closest(this.selectors.paginationLinkElement)) {
			return;
		}

		event.preventDefault();

		const pageInput = this.filterForm.elements.page;
		const oldValue = parseInt(pageInput.value);
		const newValue = target.dataset.jsProductFilterFormPaginateLink ?? 1;

		if (oldValue !== newValue) {
			pageInput.value = newValue;
		}

		const changeEvent = new Event('change', {
			bubbles: true,
			cancelable: true,
		});

		pageInput.dispatchEvent(changeEvent);
	}

	resetPage() {
		const pageInput = this.filterForm.elements.page;

		if (pageInput) {
			pageInput.value = 1;
		}
	}

	resetForm(event) {
		this.resetPage();

		const silhouetteInputs = this.filterForm.querySelectorAll(
			'[name="silhouette"]'
		);

		silhouetteInputs.forEach((element) => {
			element.removeAttribute('checked');
		});

		silhouetteInputs[0]?.setAttribute('checked', '');

		document
			.querySelectorAll('[name="brand[]"], [name="style[]"], [name="color[]"]')
			.forEach((element) => {
				element.removeAttribute('checked');
			});

		$(this.filterForm.querySelector('[name="orderby"]'))
			.find('option')
			.removeAttr('selected');

		if (
			$(this.filterForm.querySelector('[name="orderby"]')).data('ui-selectmenu')
		) {
			$(this.filterForm.querySelector('[name="orderby"]')).selectmenu(
				'refresh'
			);
		}

		this.filterForm.elements['min-price'].setAttribute(
			'value',
			this.filterForm.elements['min-price'].min
		);
		this.filterForm.elements['max-price'].setAttribute(
			'value',
			this.filterForm.elements['max-price'].max
		);

		$('#slider').slider('values', [
			parseInt(this.filterForm.elements['min-price'].value),
			parseInt(this.filterForm.elements['max-price'].value),
		]);

		this.filterForm.dispatchEvent(
			new Event('change', {
				bubbles: true,
				cancelable: true,
			})
		);
	}

	onChange(event) {
		if (event.target.form && event.target.form !== this.filterForm) {
			return;
		}

		if (event.target.name && event.target.name !== 'page') {
			this.resetPage();
		}

		// if (['brand[]', 'style[]', 'color[]'].includes(event.target.name)) {
		// 	return;
		// }

		this.updateResetButtonVisibility();

		this.getFilteredProducts();
	}

	onSubmit(event) {
		event.preventDefault();

		this.updateResetButtonVisibility();

		this.getFilteredProducts();
	}

	scrollToCatalogIfNecessary() {
		const catalogElement = document.getElementById('catalog');

		if (!catalogElement) {
			return Promise.resolve();
		}

		const catalogRect = catalogElement.getBoundingClientRect();

		if (catalogRect.top >= 0) {
			return Promise.resolve();
		}

		return scrollToElement(catalogElement, {
			duration: 1000,
			easing: function (x) {
				return x < 0.5
					? 16 * x * x * x * x * x
					: 1 - Math.pow(-2 * x + 2, 5) / 2;
			},
			align: 'start',
		});
	}

	async fetchProducts() {
		this.abortController = new AbortController();
		const signal = this.abortController.signal;

		const formData = new FormData(this.filterForm);

		try {
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
				signal,
			});

			const body = await response.json();

			console.log({ body });

			if (!body.success) {
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			if (error.name === 'AbortError') {
				return null;
			}
			throw error;
		}
	}

	async getFilteredProducts() {
		if (this.abortController) {
			this.abortController.abort();
		}

		document.documentElement.classList.add(this.stateSelectors.isLoading);
		this.filterForm.classList.add(this.stateSelectors.isLoading);
		this.contentElement.classList.add(this.stateSelectors.isLoading);

		const formData = new FormData(this.filterForm);

		try {
			const [{ value: data }, _] = await Promise.allSettled([
				this.fetchProducts(),
				this.scrollToCatalogIfNecessary(),
			]);

			console.log({ data });

			this.contentElement.innerHTML = data.feed;
			this.paginationElement.innerHTML = data.pagination;
		} catch (error) {
			if (error.name !== 'AbortError') {
				console.error(error.message);
			}
		} finally {
			document.documentElement.classList.remove(this.stateSelectors.isLoading);
			this.filterForm.classList.remove(this.stateSelectors.isLoading);
			this.contentElement.classList.remove(this.stateSelectors.isLoading);
			this.updateQueryParams(formData);

			document.dispatchEvent(
				new Event('catalog:updated', {
					bubbles: true,
					cancelable: true,
				})
			);
		}
	}

	bindEvents() {
		document.addEventListener('change', this.debouncedOnChange);
		this.filterForm.addEventListener('reset', this.resetForm);
		this.filterForm.addEventListener('submit', this.onSubmit);
		document.addEventListener('click', this.changePaginationLink);
	}

	destroy() {
		document.removeEventListener('change', this.debouncedOnChange);
		this.filterForm.removeEventListener('reset', this.resetForm);
		this.filterForm.removeEventListener('submit', this.onSubmit);
		document.removeEventListener('click', this.changePaginationLink);
	}
}

class ProductFilterFormCollection {
	/**
	 * @type {Map<string, ProductFilterForm>}
	 */
	static filterProductsForms = new Map();

	static destroyAll() {
		this.filterProductsForms.forEach((filterForm, id) => {
			filterForm.destroy();
			this.filterProductsForms.delete(id);
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((form) => {
			const productFilterFormInstance = new ProductFilterForm(form);
			this.filterProductsForms.set(form.id, productFilterFormInstance);
		});
	}
}

export default ProductFilterFormCollection;
