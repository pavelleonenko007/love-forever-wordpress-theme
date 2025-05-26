import { debounce, wait } from './utils';

const ROOT_SELECTOR = '[data-js-product-filter-form]';

class ProductFilterForm {
	selectors = {
		root: ROOT_SELECTOR,
		contentElement: '[data-js-product-filter-form-content-element]',
		paginationElement: '[data-js-product-filter-form-pagination]',
		paginationLinkElement: '[data-js-product-filter-form-paginate-link]',
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

		this.onChange = this.onChange.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
		this.debouncedOnChange = debounce(this.onChange, 200);
		this.changePaginationLink = this.changePaginationLink.bind(this);
		this.resetForm = this.resetForm.bind(this);

		this.bindEvents();
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
		this.filterForm
			.querySelector('[name="silhouette"]:checked')
			.removeAttribute('checked');
		this.filterForm
			.querySelector('[name="silhouette"]')
			.setAttribute('checked', '');

		$('#slider').slider('values', [
			parseInt(this.filterForm.elements['min-price'].min),
			parseInt(this.filterForm.elements['max-price'].max),
		]);

		this.filterForm.dispatchEvent(
			new Event('change', {
				bubbles: true,
				cancelable: true,
			})
		);
	}

	onChange(event) {
		if (event.target.name && event.target.name !== 'page') {
			this.resetPage();
		}

		if (['brand[]', 'style[]'].includes(event.target.name)) {
			return;
		}

		this.getFilteredProducts();
	}

	onSubmit(event) {
		event.preventDefault();

		this.getFilteredProducts();
	}

	scrollToCatalogIfNecessary() {
		const catalogElement = document.getElementById('catalog');

		if (catalogElement) {
			const catalogRect = catalogElement.getBoundingClientRect();

			if (catalogRect.top < 0) {
				catalogElement.scrollIntoView({ behavior: 'smooth' });
			}
		}
	}

	async getFilteredProducts() {
		if (this.abortController) {
			this.abortController.abort();
		}

		document.documentElement.classList.add(this.stateSelectors.isLoading);

		this.scrollToCatalogIfNecessary();

		this.abortController = new AbortController();
		const signal = this.abortController.signal;

		const formData = new FormData(this.filterForm);

		this.filterForm.classList.add(this.stateSelectors.isLoading);
		this.contentElement.classList.add(this.stateSelectors.isLoading);

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

			this.contentElement.innerHTML = body.data.feed;
			this.paginationElement.innerHTML = body.data.pagination;
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
		this.filterForm.addEventListener('change', this.debouncedOnChange);
		this.filterForm.addEventListener('reset', this.resetForm);
		this.filterForm.addEventListener('submit', this.onSubmit);
		document.addEventListener('click', this.changePaginationLink);
	}

	destroy() {
		this.filterForm.removeEventListener('change', this.debouncedOnChange);
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
