import BaseComponent from './BaseComponent';
import { isValidRussianPhone, promiseWrapper } from './utils';

const ROOT_SELECTOR = '[data-js-favorites-contact-form]';

class FavoritesContactForm extends BaseComponent {
	selectors = {
		root: ROOT_SELECTOR,
		successMessageElement: '[data-js-favorites-contact-form-success-message]',
		errorMessageElement: '[data-js-favorites-contact-form-error-message]',
		submitButton: '[data-js-favorites-contact-form-submit-button]',
		phoneInput: '[data-js-input-mask="phone"]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(element) {
		super(element);
		this.root = element;

		if (!this.root) {
			return;
		}

		this.successMessageElement = this.root.parentElement.querySelector(
			this.selectors.successMessageElement
		);
		this.errorMessageElement = this.root.parentElement.querySelector(
			this.selectors.errorMessageElement
		);
		this.submitButton = this.root.querySelector(this.selectors.submitButton);
		this.phoneInput = this.root.querySelector(this.selectors.phoneInput);

		this.state = this.setupFormState();

		this.bindEvents();

		this.updateUI();
	}

	setupFormState() {
		return this._getProxyState({
			phone: this.phoneInput.value,
			isSubmitting: false,
			errors: [],
			success: false,
			message: '',
		});
	}

	async submitData() {
		try {
			const formData = new FormData(this.root);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			if (!response.ok) {
				console.error({ body });
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	bindEvents() {
		this.phoneInput.addEventListener('input', this.onPhoneInput);
		document.addEventListener('submit', this.onSubmit);
	}

	onPhoneInput = (event) => {
		this.state.phone = event.target.value;
	};

	onSubmit = async (event) => {
		if (event.target !== this.root) {
			return;
		}

		event.preventDefault();

		if (this.state.isSubmitting) {
			return;
		}

		this.state.isSubmitting = true;

		const { data, error } = await promiseWrapper(this.submitData());

		// console.log({ data, error });

		this.state.isSubmitting = false;

		if (error) {
			this.state.errors = [error.message];
			setTimeout(() => {
				this.resetForm(false);
			}, 4_000);
			return;
		}

		this.state.success = true;
		this.state.message = data.message;

		setTimeout(() => {
			this.resetForm(true);
		}, 4_000);
	};

	resetForm(clearData = false) {
		if (clearData) {
			this.state.phone = '';
		}

		this.state.isSubmitting = false;
		this.state.errors = [];
		this.state.success = false;
		this.state.message = '';
	}

	updateUI() {
		this.phoneInput.value = this.state.phone.trim();
		
		this.submitButton.disabled =
			this.state.isSubmitting || !isValidRussianPhone(this.state.phone);

		this.submitButton.textContent = this.state.isSubmitting
			? 'Отправка...'
			: 'Отправить';

		this.root.style.display = this.state.success ? 'none' : null;
		this.root.tabIndex = this.state.success ? '-1' : '0';

		this.successMessageElement.style.display = this.state.success ? 'block' : null;
		this.successMessageElement.tabIndex = this.state.success ? '0' : '-1';
		this.successMessageElement.innerHTML = this.state.message;

		this.errorMessageElement.style.display = this.state.errors.length > 0 ? 'block' : null;
		this.errorMessageElement.tabIndex = this.state.errors.length > 0 ? '0' : '-1';

		this.errorMessageElement.innerHTML = this.state.errors
			.map((error) => `<span>${error}</span>`)
			.join('');
	}
}

export default class FavoritesContactFormCollection {
	static favoritesContactForms = new Map();

	static init() {
		const favoritesContactForms = document.querySelectorAll(ROOT_SELECTOR);
		favoritesContactForms.forEach((element) => {
			new FavoritesContactForm(element);
		});
	}
}
