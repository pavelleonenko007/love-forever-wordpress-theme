import { isValidRussianPhone, promiseWrapper } from './utils';

const ROOT_SELECTOR = '[data-js-callback-form]';
const FORM_STATUSES = {
	success: 'Ваша заявка отправлена',
	error: 'Ошибка',
	submitting: 'Отправка...',
	idle: 'Заказать звонок',
};

class CallbackForm {
	selectors = {
		root: ROOT_SELECTOR,
		submitButton: '[data-js-callback-form-submit-button]',
		errors: '[data-js-callback-form-errors]',
	};

	#state = {
		name: '',
		phone: '',
		status: FORM_STATUSES.idle,
		errors: [],
	};

	/**
	 *
	 * @param {HTMLFormElement} form
	 */
	constructor(form) {
		this.form = form;
		this.submitButton = this.form.querySelector(this.selectors.submitButton);
		this.errors = this.form.querySelector(this.selectors.errors);
		this.dialogTitle = this.form
			.closest('[data-js-dialog]')
			.querySelector('[data-js-dialog-title]');

		this.#state = new Proxy(this.#state, {
			get: (target, key) => {
				return target[key];
			},
			set: (target, key, value) => {
				const oldValue = target[key];
				target[key] = value;

				if (oldValue !== value) {
					this.updateUI();
				}

				return true;
			},
		});

		this.bindEvents();
		this.updateUI();
	}

	async requestCallback() {
		try {
			const formData = new FormData(this.form);
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			console.log({ body });

			if (!body.success) {
				this.#state.errors = body.data.errors;
				throw new Error(body.data.message);
			}

			this.#state.status = FORM_STATUSES.success;

			return body;
		} catch (error) {
			throw error;
		}
	}

	onPhoneInput = (event) => {
		this.#state.phone = event.target.value;
	}

	onSubmit = async (event) => {
		if (this.#state.status === FORM_STATUSES.submitting) {
			return;
		}

		event.preventDefault();

		this.#state.status = FORM_STATUSES.submitting;

		const { data, error } = promiseWrapper(this.requestCallback());

		if (error) {
			this.#state.status = FORM_STATUSES.error;
			return;
		}

		this.#state.status = FORM_STATUSES.success;

		setTimeout(() => {
			this.reset();
		}, 3_000);
	};

	bindEvents() {
		Array.from(this.form.elements)
			.filter((field) => field.name)
			.forEach((field, index) => {
				field.addEventListener('change', (event) => {
					if (field.type === 'checkbox') {
						this.#state[field.name] = field.checked;
					} else {
						this.#state[field.name] = field.value;
					}
				});

				field.addEventListener('blur', (event) => {
					this.#state.status = FORM_STATUSES.idle;
				});
			});
		
		this.form.elements.phone.addEventListener('input', this.onPhoneInput);
		this.form.addEventListener('submit', this.onSubmit);
	}

	reset() {
		this.#state.name = '';
		this.#state.phone = '';
		this.#state.status = FORM_STATUSES.idle;
		this.#state.errors = [];
	}

	updateUI() {
		this.form.elements.name.value = this.#state.name;
		this.form.elements.phone.value = this.#state.phone;

		this.submitButton.disabled =
			this.#state.name === '' ||
			this.#state.phone === '' ||
			!isValidRussianPhone(this.#state.phone) ||
			this.#state.status === FORM_STATUSES.submitting;

		this.submitButton.textContent = this.#state.status;

		this.form.hidden = this.#state.status === FORM_STATUSES.success;

		this.dialogTitle.textContent = this.#state.status;

		this.errors.hidden = this.#state.errors.length === 0;
		this.errors.innerHTML = this.#state.errors
			.map((error) => `<span>${error}</span>`)
			.join('');
	}
}

export class CallbackFormCollection {
	static callbackForms = new Map();

	static init() {
		const callbackForms = document.querySelectorAll(ROOT_SELECTOR);

		callbackForms.forEach((form) => {
			const callbackForm = new CallbackForm(form);
			this.callbackForms.set(form, callbackForm);
		});
	}
}
