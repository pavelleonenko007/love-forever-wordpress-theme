import { promiseWrapper } from './utils';
const ROOT_SELECTOR = '[data-js-filter-fitting-form]';

class FilterFittingForm {
	selectors = {
		root: ROOT_SELECTOR,
		fittingsContainer: '[data-js-filter-fitting-form-fitting-container]',
		quickDateSelectButton: '[data-js-filter-fitting-form-date-button]',
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
		this.fittingContainerElement = this.root.parentElement.querySelector(
			this.selectors.fittingsContainer
		);
		this.quickDateSelectButtons = this.root.querySelectorAll(
			this.selectors.quickDateSelectButton
		);
		this.abortController = new AbortController();
		this.signal = this.abortController.signal;

		this.bindEvents();
	}

	async getFilteredFittings() {
		try {
			const formData = new FormData(this.root);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
				signal: this.signal,
			});

			const body = await response.json();

			if (!body.success) {
				console.error(body.data.debug);
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

	async filterFittings() {
		if (
			this.fittingContainerElement.classList.contains(
				this.stateSelectors.isLoading
			)
		) {
			this.abortController.abort();
			this.abortController = new AbortController();
			this.signal = this.abortController.signal;

			this.fittingContainerElement.classList.remove(
				this.stateSelectors.isLoading
			);

			return this.filterFittings();
		}

		this.fittingContainerElement.classList.add(this.stateSelectors.isLoading);

		const { data, error } = await promiseWrapper(this.getFilteredFittings());

		console.log({ data, error });

		if (error) {
			console.error(error);

			alert(error.message);
			return;
		}

		if (data) {
			this.fittingContainerElement.innerHTML = data.html;
		}

		this.fittingContainerElement.classList.remove(
			this.stateSelectors.isLoading
		);
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick = (event) => {
		event.preventDefault();

		const date = event.target.dataset.jsFilterFittingFormDateButton;

		if (!date) return;

		const dateControl = this.root.elements.date;

		dateControl.value = date;

		dateControl.dispatchEvent(
			new Event('change', {
				bubbles: true,
			})
		);
	};

	/**
	 *
	 * @param {Event} event
	 */
	onChange = (event) => {
		console.log('change');

		this.filterFittings();
	};

	/**
	 *
	 * @param {SubmitEvent} event
	 */
	onSubmit = (event) => {
		console.log('submit');
		event.preventDefault();

		this.filterFittings();
	};

	onReset = (event) => {
		this.root.reset();

		this.root
			.querySelectorAll('input:not([type="hidden"])')
			.forEach((input) => {
				input.value = '';
				input.removeAttribute('checked');
				input.removeAttribute('selected');
			});

		this.filterFittings();
	};

	bindEvents() {
		this.root.addEventListener('change', this.onChange);
		this.root.addEventListener('submit', this.onSubmit);
		this.root.addEventListener('reset', this.onReset);
		this.quickDateSelectButtons.forEach((button) =>
			button.addEventListener('click', this.onClick)
		);
		document.addEventListener('updateFittings', this.onChange);
	}

	destroy() {
		this.root.removeEventListener('change', this.onChange);
		this.root.removeEventListener('submit', this.onSubmit);
		this.root.removeEventListener('reset', this.onReset);
		this.quickDateSelectButtons.forEach((button) =>
			button.removeEventListener('click', this.onClick)
		);
		document.removeEventListener('updateFittings', this.onChange);
	}
}

export default class FilterFittingFormCollection {
	/**
	 * @type {Map<HTMLFormElement, FilterFittingForm>} filterFittingForms
	 */
	static filterFittingForms = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((filterForm) => {
			const filterFittingFormInstance = new FilterFittingForm(filterForm);

			this.filterFittingForms.set(filterForm, filterFittingFormInstance);
		});
	}

	static destroyAll() {
		this.filterFittingForms.forEach((instance) => {
			instance.destroy();
		});
		this.filterFittingForms.clear();
	}
}
