import DialogCollection from './Dialog';

const ROOT_SELECTOR = '[data-js-review-form]';

class ReviewForm {
	selectors = {
		root: ROOT_SELECTOR,
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
		this.dialogId = 'reviewDialog';

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
		const dialogInstance = DialogCollection.getDialogsById(this.dialogId);
		console.log({ dialogInstance });

		const dialogTitle = dialogInstance.dialog.querySelector(
			'.review-card__title'
		);
		const dialogDescrition = dialogInstance.dialog.querySelector(
			'.review-card__description'
		);

		dialogTitle.textContent = 'Ошибка';
		dialogDescrition.textContent = error;

		dialogInstance.open();
	}

	showSuccessMessage(message) {
		const dialogInstance = DialogCollection.getDialogsById(this.dialogId);
		const dialogTitle = dialogInstance.dialog.querySelector(
			'.review-card__title'
		);
		const dialogDescrition = dialogInstance.dialog.querySelector(
			'.review-card__description'
		);

		dialogTitle.textContent = 'Спасибо за ваш отзыв';
		dialogDescrition.textContent = message;

		dialogInstance.open();
	}

	resetForm() {
		this.root.reset();

		this.root.querySelectorAll('[type="file"]').forEach((input) => {
			input.value = '';
			input.dispatchEvent(
				new Event('change', {
					bubbles: true,
				})
			);
		});
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
		} catch (error) {
			console.error(error);
			this.showGlobalError(error.message);
		} finally {
			this.root.classList.remove(this.stateSelectors.isLoading);
		}
	}

	/**
	 * @param {CustomEvent} event
	 */
	onDialogClose = (event) => {
		if (event.detail.dialogId === this.dialogId) {
			this.resetForm();
		}
	};

	bindEvents() {
		document.addEventListener('submit', this.onSubmit);
		window.addEventListener('dialogClose', this.onDialogClose);
	}

	destroy() {
		document.removeEventListener('submit', this.onSubmit);
		window.removeEventListener('dialogClose', this.onDialogClose);
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
