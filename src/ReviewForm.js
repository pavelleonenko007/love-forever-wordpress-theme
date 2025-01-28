const ROOT_SELECTOR = '[data-js-review-form]';

class ReviewForm {
	selectors = {
		root: ROOT_SELECTOR,
		errorMessageElementSelector: '[data-js-form-error-messages]',
		globalErrorElementSelector: '[data-js-review-form-global-error]',
		successMessageElementSelector: '[data-js-review-form-success-message]',
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

		this.onSubmit = this.onSubmit.bind(this);

		this.bindEvents();
	}

	/**
	 *
	 * @param {Object} errors
	 */
	showErrors(errors) {
		let firstErrorField = null;

		for (const [name, errorMessage] of Object.entries(errors)) {
			const targetField = this.root.querySelector(`[name="${name}"]`);

			if (!targetField) {
				continue;
			}

			targetField.ariaInvalid = true;

			if (!firstErrorField) {
				firstErrorField = targetField;
				targetField.focus();
			}

			const errorMessagesElement = targetField.parentElement.querySelector(
				this.selectors.errorMessageElementSelector
			);

			if (errorMessagesElement) {
				errorMessagesElement.innerHTML = errorMessage;
			}
		}
	}

	showGlobalError(error) {
		this.root.hidden = true;

		const globalErrorElement = this.root.parentElement.querySelector(
			this.selectors.globalErrorElementSelector
		);

		globalErrorElement.innerHTML = error;
		globalErrorElement.style.display = 'block';
	}

	clearErrors() {
		this.root
			.querySelectorAll(this.selectors.errorMessageElementSelector)
			.forEach((errorMessagesElement) => {
				errorMessagesElement.innerHTML = '';
			});
		this.root.querySelectorAll('[aria-invalid]').forEach((invalidField) => {
			invalidField.ariaInvalid = false;
		});
		this.root.parentElement.querySelector(
			this.selectors.globalErrorElementSelector
		).style.display = 'none';
	}

	showSuccessMessage(message) {
		this.root.hidden = true;

		const successMessageElement = this.root.parentElement.querySelector(
			this.selectors.successMessageElementSelector
		);

		successMessageElement.innerHTML = message;
		successMessageElement.style.display = 'block';
	}

	resetForm() {
		this.root.reset();
		this.clearErrors();

		this.root.querySelectorAll('[type="file"]').forEach((input) => {
			input.dispatchEvent(
				new Event('change', {
					bubbles: false,
				})
			);
		});

		this.root.hidden = false;
		this.root.parentElement.querySelector(
			this.selectors.successMessageElementSelector
		).style.display = 'none';
	}

	/**
	 *
	 * @param {Event<SubmitEvent>} event
	 */
	async onSubmit(event) {
		if (event.target !== this.root) {
			return;
		}

		event.preventDefault();

		if (this.root.classList.contains(this.stateSelectors.isLoading)) {
			return;
		}

		this.root.classList.add(this.stateSelectors.isLoading);

		try {
			const formData = new FormData(this.root);
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			console.log(body);

			if (!response.ok) {
				if (body.data.errors) {
					this.showErrors(body.data.errors);
				} else {
					throw new Error(body.data.message);
				}
			}

			this.showSuccessMessage(body.data.message);

			setTimeout(() => {
				this.resetForm();
			}, 3_000);
		} catch (error) {
			console.error(error);
			this.showGlobalError(error.message);
		} finally {
			this.root.classList.remove(this.stateSelectors.isLoading);
		}
	}

	bindEvents() {
		document.addEventListener('submit', this.onSubmit);
	}

	destroy() {
		document.removeEventListener('submit', this.onSubmit);
	}
}

export default class ReviewFormCollection {
	/**
	 * @type {Map<string, ReviewForm>}
	 */
	static reviewForms = new Map();

	static getReviewFormById(id) {
		this.reviewForms.get(id);
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((root) => {
			const reviewForm = new ReviewForm(root);
			this.reviewForms.set(root.id, reviewForm);
		});
	}

	static destroyAll() {
		this.reviewForms.forEach((reviewForm) => {
			reviewForm.destroy();
		});
	}
}
