import Barba from 'barba.js';
import { debounce, promiseWrapper } from './utils';

const ROOT_SELECTOR = '[data-js-search-form]';

class SearchForm {
	selectors = {
		root: ROOT_SELECTOR,
		searchInput: '[data-js-search-form-search-input]',
		searchResults: '[data-js-search-form-results]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};
	/**
	 *
	 * @param {HTMLFormElement} element
	 */
	constructor(element) {
		this.root = element;
		this.searchInput = this.root.querySelector(this.selectors.searchInput);
		this.searchResultsElement = this.root.parentElement.querySelector(
			this.selectors.searchResults
		);

		this.onDebouncedInput = debounce(this.onInput, 500);

		this.bindEvents();
	}

	async queryProducts() {
		const formData = new FormData(this.root);

		if (!formData.get('s')) {
			return { html: '' };
		}

		formData.append('action', 'query_products');
		formData.append('nonce', LOVE_FOREVER.NONCE);

		try {
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			console.log({ body });

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	onReset = () => {
		this.root.reset();
		this.searchResultsElement.innerHTML = '';
		this.searchInput.focus();
	};

	/**
	 *
	 * @param {InputEvent} event
	 */
	onInput = async (event) => {
		if (this.root.classList.contains(this.stateSelectors.isLoading)) {
			return;
		}

		this.root.classList.add(this.stateSelectors.isLoading);

		const { data, error } = await promiseWrapper(this.queryProducts());

		this.root.classList.remove(this.stateSelectors.isLoading);

		if (error) {
			console.error(error);
			alert(error.message);
			return;
		}

		this.searchResultsElement.innerHTML = data.html;
	};

	/**
	 * @param {SubmitEvent} event
	 */
	onSubmit = (event) => {
		event.preventDefault();

		const formData = new FormData(this.root);
		const queryParams = new URLSearchParams(Object.fromEntries(formData));

		Barba.Pjax.goTo(`${window.location.origin}/?${queryParams.toString()}`);
	};

	bindEvents() {
		this.searchInput.addEventListener('input', this.onDebouncedInput);
		this.root.addEventListener('reset', this.onReset);
		this.root.addEventListener('submit', this.onSubmit);
	}

	destroy() {
		this.searchInput.removeEventListener('input', this.onDebouncedInput);
		this.root.removeEventListener('reset', this.onReset);
		this.root.removeEventListener('submit', this.onSubmit);
	}
}

export default class SearchFormCollection {
	/**
	 * @type {Map<HTMLFormElement, SearchForm>}
	 */
	static searchForms = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((form) => {
			const formInstance = new SearchForm(form);
			this.searchForms.set(form, formInstance);
		});
	}

	static destroyAll() {
		this.searchForms.forEach((formInstance, formElement) => {
			formInstance.destroy();
		});
		this.searchForms.clear();
	}
}
