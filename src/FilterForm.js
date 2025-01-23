import { debounce } from './utils';

const ROOT_SELECTOR = '[data-js-filter-form]';

class FilterForm {
	selectors = {
		root: ROOT_SELECTOR,
		contentElement: '[data-js-filter-form-content-element]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(element) {
		this.filterForm = element;
		this.contentElement = document.querySelector(this.selectors.contentElement);

		this.controller = new AbortController();
		this.signal = this.controller.signal;

		this.getFilteredProducts = this.getFilteredProducts.bind(this);
		this.debouncedGetFilteredProducts = debounce(this.getFilteredProducts, 200);

		this.bindEvents();
	}

	async getFilteredProducts(event) {
		if (this.filterForm.classList.contains(this.stateSelectors.isLoading)) {
			this.controller.abort();
		}

		const formData = new FormData(this.filterForm);

		this.filterForm.classList.add(this.stateSelectors.isLoading);
		this.contentElement.classList.add(this.stateSelectors.isLoading);

		try {
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
				signal: this.signal,
			});

			const body = await response.json();

			if (!body.success) {
				throw new Error(body.data.message);
			}

			this.contentElement.innerHTML = body.data.html;
		} catch (error) {
			if (error.name !== 'AbortError') {
				console.error(error.message);
			}
		} finally {
			this.filterForm.classList.remove(this.stateSelectors.isLoading);
			this.contentElement.classList.remove(this.stateSelectors.isLoading);
		}
	}

	bindEvents() {
		this.filterForm.addEventListener(
			'change',
			this.debouncedGetFilteredProducts
		);
	}
}

class FilterFormCollection {
	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((form) => {
			new FilterForm(form);
		});
	}
}

export default FilterFormCollection;
