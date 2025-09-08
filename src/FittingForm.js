import BaseComponent from './BaseComponent';
import DialogCollection from './Dialog';
import {
	formatDateToRussian,
	isSafariBrowser,
	isValidRussianPhone,
	promiseWrapper,
	wait,
} from './utils';

const ROOT_SELECTOR = '[data-js-fitting-form]';

const getAvailableSlotsForDate = async (date) => {
	try {
		const formData = new FormData();
		formData.append('action', 'get_fitting_time_slots');
		formData.append('nonce', LOVE_FOREVER.NONCE);
		formData.append('date', date);

		const response = await fetch(LOVE_FOREVER.AJAX_URL, {
			method: 'POST',
			body: formData,
		});

		const body = await response.json();

		// console.log(body);

		if (!body.success) {
			console.error(body.data.debug);
			throw new Error(body.data.message);
		}

		return body.data;
	} catch (error) {
		throw error;
	}
};

/**
 * @param {FormData} formData
 */
const createFittingRecord = async (formData) => {
	try {
		const response = await fetch(LOVE_FOREVER.AJAX_URL, {
			method: 'POST',
			body: formData,
		});

		const body = await response.json();

		// console.log(body);

		if (!body.success) {
			console.error(body.data.debug);
			throw new Error(body.data.message);
		}

		return body.data;
	} catch (error) {
		throw error;
	}
};

window.addEventListener('fitting:datetimechange', (event) => {
	const globalFittingDialog = DialogCollection.getDialogsById(
		'globalFittingDialog'
	)?.dialog;
	const singleProductFittingDialog = DialogCollection.getDialogsById(
		'singleProductFittingDialog'
	)?.dialog;

	[globalFittingDialog, singleProductFittingDialog].forEach((dialog) => {
		const dialogTimeElement = dialog.querySelector(
			'[data-js-fitting-form-selected-date]'
		);

		if (dialogTimeElement) {
			console.log({ dialogTimeElement });

			dialogTimeElement.textContent = formatDateToRussian(
				event.detail.datetime
			);
		}
	});
});

window.addEventListener('fitting:created', (event) => {
	const messageElements = document.querySelectorAll('[data-js-dialog-title]');

	messageElements.forEach((messageElement) => {
		messageElement.textContent = event.detail.message;
	});

	const globalFittingDialog = DialogCollection.getDialogsById(
		'globalFittingDialog'
	)?.dialog;
	const singleProductFittingDialog = DialogCollection.getDialogsById(
		'singleProductFittingDialog'
	)?.dialog;

	[globalFittingDialog, singleProductFittingDialog].forEach((dialog) => {
		const closeButtons = dialog.querySelectorAll(
			'[data-js-dialog-close-button]'
		);
		const fieldsContainerElement = dialog.querySelector(
			'.fitting-form__group-body'
		);
		const legalElement = dialog.querySelector('.fitting-form__group-footer');

		closeButtons.forEach((closeButton) => {
			closeButton.disabled = false;
			closeButton.hidden = false;
		});

		if (fieldsContainerElement) {
			fieldsContainerElement.hidden = true;
		}

		if (legalElement) {
			legalElement.hidden = true;
		}
	});
});

window.addEventListener('fitting:createerror', (event) => {
	const errorElements = document.querySelectorAll(
		'[data-js-fitting-form-errors]'
	);

	errorElements.forEach((errorElement) => {
		errorElement.textContent = event.detail.message;
		errorElement.hidden = false;

		setTimeout(() => {
			errorElement.textContent = '';
			errorElement.hidden = true;
		}, 3_000);
	});
});

class FittingForm {
	selectors = {
		root: ROOT_SELECTOR,
	};

	/**
	 * @param {HTMLFormElement} element
	 */
	constructor(element) {
		this.root = element;
		this.submitButton = Array.from(
			document.querySelectorAll('button[type="submit"]')
		).find((button) => button.form === this.root);

		this.hasBeenSubmitted = false;

		this.bindEvents();

		this.validateForm();

		this.emitDateTimeUpdate();
	}

	emitDateTimeUpdate() {
		const datetime = `${this.root.date.value} ${this.root.time.value}`;

		window.dispatchEvent(
			new CustomEvent('fitting:datetimechange', {
				detail: {
					datetime,
				},
			})
		);
	}

	async updateTimeSlots() {
		console.log('updateTimeSlots');

		/**
		 * @type {HTMLSelectElement}
		 */
		const timeControl = this.root.time;
		const $customSelectInstance = $(timeControl).selectmenu('instance');

		timeControl.disabled = true;

		if ($customSelectInstance !== undefined) {
			$(timeControl).selectmenu('disable');
		}

		const { data, error } = await promiseWrapper(
			getAvailableSlotsForDate(this.root.date.value)
		);

		if (error) {
			alert(error);
			return;
		}

		timeControl.innerHTML = '';

		for (const time in data.slots) {
			const slot = data.slots[time];
			const option = document.createElement('option');

			option.value = time;
			option.textContent = time;
			option.disabled = slot.available < 1;

			timeControl.append(option);
		}

		timeControl.disabled = false;

		if ($customSelectInstance !== undefined) {
			$(timeControl).selectmenu('enable');
			$(timeControl).selectmenu('refresh');
		}

		timeControl.dispatchEvent(
			new Event('change', {
				bubbles: true,
			})
		);
	}

	validateForm() {
		const requiredFields = ['date', 'time', 'name', 'phone'];
		const formData = new FormData(this.root);

		let isValid = true;

		for (let [name, value] of formData) {
			if (!requiredFields.includes(name)) {
				continue;
			}

			value = value.trim();

			if (!value) {
				isValid = false;
				break;
			}
		}

		const submitButton = Array.from(
			document.querySelectorAll('[data-js-fitting-form-submit-button]')
		).find((button) => button.form === this.root);

		if (submitButton) {
			submitButton.disabled = !isValid;
		}
	}

	onChange = (event) => {
		if (event.target.form !== this.root) {
			return;
		}

		if (event.target.name === 'date') {
			this.updateTimeSlots();
		}

		if (event.target.name === 'time') {
			this.emitDateTimeUpdate();
		}

		this.validateForm();
	};

	/**
	 * @param {SubmitEvent} event
	 */
	onSubmit = async (event) => {
		event.preventDefault();

		this.submitButton.disabled = true;

		const formData = new FormData(this.root);

		formData.append('action', 'create_new_fitting_record');

		const { data, error } = await promiseWrapper(createFittingRecord(formData));

		this.hasBeenSubmitted = true;

		this.submitButton.disabled = false;

		if (error) {
			window.dispatchEvent(
				new CustomEvent('fitting:createerror', {
					detail: {
						message: error,
					},
				})
			);
			return;
		}

		window.dispatchEvent(
			new CustomEvent('fitting:created', {
				detail: {
					message: data.message,
				},
			})
		);
	};

	/**
	 * @param {CustomEvent} event
	 */
	onDialogClose = (event) => {
		if (
			!['singleProductFittingDialog', 'globalFittingDialog'].includes(
				event.detail.dialogId
			)
		) {
			return;
		}

		if (!this.hasBeenSubmitted) {
			return;
		}

		this.root.reset();

		const { dialog } = DialogCollection.getDialogsById(event.detail.dialogId);

		const dialogTitle = dialog.querySelector('[data-js-dialog-title]');
		const fieldsContainerElement = dialog.querySelector(
			'.fitting-form__group-body'
		);
		const legalElement = dialog.querySelector('.fitting-form__group-footer');
		const closeButton = dialog.querySelector('.dialog-card__body-button');

		dialogTitle.textContent = 'Запись на примерку';

		if (fieldsContainerElement) {
			fieldsContainerElement.hidden = false;
		}

		if (legalElement) {
			legalElement.hidden = false;
		}

		if (closeButton) {
			closeButton.hidden = true;
			closeButton.disabled = true;
		}

		this.updateTimeSlots();
	};

	bindEvents() {
		document.addEventListener('change', this.onChange);
		this.root.addEventListener('submit', this.onSubmit);
		document.addEventListener('dialogClose', this.onDialogClose);
	}
}

class BaseFittingForm extends BaseComponent {
	/**
	 *
	 * @param {HTMLFormElement} form
	 */
	constructor(form) {
		super();
		this.form = form;

		this.state = this._getProxyState({
			success: false,
			error: null,
			isSubmitting: false,
			name: this.form.elements.name.value,
			phone: this.form.elements.phone.value,
			date:
				this.form.elements.date instanceof RadioNodeList
					? Array.from(this.form.elements.date).find((input) => !input.disabled)
							.value
					: this.form.elements.date.value,
			time: this.form.elements.time.value,
		});
	}

	// Shared methods
	/**
	 *
	 * @param {SubmitEvent} event
	 */
	async submitForm(event) {
		event.preventDefault();

		if (this.state.isSubmitting) return;

		this.state.isSubmitting = true;
		this.state.error = null;

		try {
			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: new FormData(this.form),
			});
			const body = await response.json();

			// console.log(body);

			if (!data.success) {
				throw new Error(data.message);
			}

			this.state.success = true;
			this.state.dialogMessage = data.message;
		} catch (error) {
			this.state.error = error.message;
		} finally {
			this.state.isSubmitting = false;
		}
	}

	destroy() {
		this.form.removeEventListener('submit', this.submitForm);
	}
}

class GlobalFittingForm extends BaseFittingForm {
	selectors = {
		step: '[data-js-fitting-form-step]',
		dateInput: '[data-js-fitting-form-date-control]',
		backButton: '[data-js-fitting-form-back-button]',
		nextButton: '[data-js-fitting-form-next-button]',
		submitButton: '[data-js-fitting-form-submit-button]',
		errorsElement: '[data-js-fitting-form-errors]',
		closestDialog: '[data-js-dialog]',
		dialogTitle: '[data-js-dialog-title]',
		slotsContainer: '[data-js-fitting-form-slots-container]',
		nextSlotsButton: '[data-js-fitting-form-next-slots-button]',
		prevSlotsButton: '[data-js-fitting-form-prev-slots-button]',
		dialogSelectedTime: '[data-js-fitting-form-selected-date]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(form) {
		super(form);

		this.steps = this.form.querySelectorAll(this.selectors.step);
		this.backButton = this.form.querySelector(this.selectors.backButton);
		this.nextButton = this.form.querySelector(this.selectors.nextButton);
		this.slotsContainer = this.form.querySelector(
			this.selectors.slotsContainer
		);
		this.prevSlotsButton = this.form.querySelector(
			this.selectors.prevSlotsButton
		);
		this.nextSlotsButton = this.form.querySelector(
			this.selectors.nextSlotsButton
		);
		this.submitButton = this.form.querySelector(this.selectors.submitButton);
		this.errorsElement = this.form.querySelector(this.selectors.errorsElement);
		this.closestDialog = this.form.closest(this.selectors.closestDialog);
		this.dialogTitle = this.closestDialog?.querySelector(
			this.selectors.dialogTitle
		);
		this.dialogSelectedTime = this.closestDialog.querySelector(
			this.selectors.dialogSelectedTime
		);

		this.state = this._getProxyState({
			...this.state,
			dateIncrementRatio: 0,
			dialogMessage: 'Запись на примерку',
			step: 0,
			dress_category: null,
			isUpdatingSlots: false,
			isSubmitting: false,
		});

		this.prevState = { ...this.state };

		this.changeFormHandler = this.changeFormHandler.bind(this);
		this.submitForm = this.submitForm.bind(this);
		this.closeDialogHandler = this.closeDialogHandler.bind(this);
		this.openDialogHandler = this.openDialogHandler.bind(this);
		this.prevStep = this.prevStep.bind(this);
		this.prevDates = this.prevDates.bind(this);
		this.nextDates = this.nextDates.bind(this);

		this.dialogSelectedTime.textContent = formatDateToRussian(
			`${this.state.date} ${this.state.time}`
		);

		this.bindEvents();
	}

	_normalizeStep(stepNumber) {
		if (stepNumber < 0) {
			return 0;
		}

		if (stepNumber >= this.steps.length) {
			return this.steps.length - 1;
		}

		return stepNumber;
	}

	_normalizeDateIncrement(number) {
		if (number < 0) {
			return 0;
		}

		return number;
	}

	/**
	 *
	 * @param {ChangeEvent} event
	 */
	changeFormHandler(event) {
		const { target } = event;

		if (target.dataset?.jsFittingFormDateValue) {
			this.form.querySelector(this.selectors.dateInput).value =
				target.dataset.jsFittingFormDateValue;
		}

		const formState = Object.fromEntries(new FormData(this.form));

		for (const key in formState) {
			if (Object.prototype.hasOwnProperty.call(formState, key)) {
				const value = formState[key];
				this.state[key] = value;
			}
		}
	}

	getDateTimeSlots() {
		if (this.state.isUpdatingSlots) {
			return;
		}

		this.state.isUpdatingSlots = true;

		const formData = new FormData();

		formData.append('action', 'get_date_time_slots');
		formData.append('nonce', LOVE_FOREVER.NONCE);
		formData.append('date-increment-ratio', this.state.dateIncrementRatio);

		fetch(LOVE_FOREVER.AJAX_URL, {
			method: 'POST',
			body: formData,
		})
			.then((response) => response.json())
			.then(({ success, data }) => {
				if (!success) {
					throw new Error(data.message);
				}

				this.slotsContainer.innerHTML = data.html;
			})
			.catch((error) => {
				console.error(error);
				this.slotsContainer.innerHTML = `<p class="fitting-form__columns-error">${error.message}</p>`;
			})
			.finally(() => {
				this.state.isUpdatingSlots = false;
			});
	}

	nextDates() {
		this.state.dateIncrementRatio = this.state.dateIncrementRatio + 1;
	}

	prevDates() {
		this.state.dateIncrementRatio = this._normalizeDateIncrement(
			this.state.dateIncrementRatio - 1
		);
	}

	async submitForm(event) {
		event.preventDefault();

		if (this.state.isSubmitting) {
			return;
		}

		this.state.isSubmitting = true;
		// this.state.error = null;

		try {
			const formData = new FormData(this.form);
			formData.append('action', 'create_new_fitting_record');

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			// console.log(body);

			this.state.success = body.success;

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			this.state.dialogMessage = body.data.message;
			this.state.dateIncrementRatio = 1;

			document.dispatchEvent(
				new Event('updateFittings', {
					bubbles: true,
					cancelable: true,
				})
			);
		} catch (error) {
			console.error(error);
			this.state.error = error.message;
		} finally {
			this.state.isSubmitting = false;
		}
	}

	closeDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.reset();
		}
	}

	openDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.getDateTimeSlots();
		}
	}

	reset() {
		this.form.reset();
		this.state.success = false;
		this.state.dialogMessage = 'Запись на примерку';
		this.state.step = 0;
		this.state.dateIncrementRatio = 0;
		this.form.dispatchEvent(new Event('change'));
	}

	openStep(stepNumber) {
		const stepToOpen = this._normalizeStep(stepNumber);

		this.steps.forEach((stepElement, index) => {
			stepElement.hidden = index !== stepToOpen;
		});

		this.state.step = stepToOpen;
	}

	prevStep() {
		this.openStep(this.state.step - 1);
	}

	nextStep() {
		this.openStep(this.state.step + 1);
	}

	updateUI() {
		this.backButton.disabled =
			this.state.step === 0 || this.state.success === true;

		if (this.prevState.step !== this.state.step) {
			this.openStep(this.state.step);
		}

		if (
			this.prevState.time !== this.state.time ||
			this.prevState.date !== this.state.date
		) {
			this.state.step = 1;
		}

		this.slotsContainer.classList.toggle(
			this.stateSelectors.isLoading,
			this.state.isUpdatingSlots
		);
		this.prevSlotsButton.disabled = this.state.isUpdatingSlots;
		this.nextSlotsButton.disabled = this.state.isUpdatingSlots;

		if (!this.state.isUpdatingSlots) {
			this.prevSlotsButton.disabled = this.state.dateIncrementRatio === 0;
		}

		if (this.prevState.dateIncrementRatio !== this.state.dateIncrementRatio) {
			this.getDateTimeSlots();
		}

		if (this.state.date && this.state.time) {
			this.dialogSelectedTime.textContent = formatDateToRussian(
				`${this.state.date} ${this.state.time}`
			);
		} else {
			this.dialogSelectedTime.textContent = '';
		}

		this.errorsElement.innerHTML = `<p>${this.state.error}</p>`;
		this.errorsElement.hidden = !Boolean(this.state.error);

		this.submitButton.disabled = this.state.isSubmitting;

		if (this.dialogTitle) {
			this.dialogTitle.textContent = this.state.dialogMessage;
		}

		this.form.hidden = this.state.success;

		if (this.closestDialog) {
			this.form.nextElementSibling.hidden = !this.state.success;
			this.form.nextElementSibling.disabled = !this.state.success;
		}

		this.prevState = { ...this.state };
	}

	bindEvents() {
		this.form.addEventListener('change', this.changeFormHandler);
		this.form.addEventListener('submit', this.submitForm);
		this.backButton.addEventListener('click', this.prevStep);
		this.prevSlotsButton.addEventListener('click', this.prevDates);
		this.nextSlotsButton.addEventListener('click', this.nextDates);
		document.addEventListener('dialogClose', this.closeDialogHandler);
		document.addEventListener('dialogOpen', this.openDialogHandler);
	}

	destroy() {
		this.form.removeEventListener('change', this.changeFormHandler);
		this.form.removeEventListener('submit', this.submitForm);
		this.backButton.removeEventListener('click', this.prevStep);
		this.prevSlotsButton.removeEventListener('click', this.prevDates);
		this.nextSlotsButton.removeEventListener('click', this.nextDates);
		document.removeEventListener('dialogClose', this.closeDialogHandler);
		document.removeEventListener('dialogOpen', this.openDialogHandler);
	}
}

class GlobalFittingFormSimpler extends BaseFittingForm {
	selectors = {
		dateInput: '[data-js-fitting-form-date-control]',
		submitButton: '[data-js-fitting-form-submit-button]',
		errorsElement: '[data-js-fitting-form-errors]',
		closestDialog: '[data-js-dialog]',
		dialogTitle: '[data-js-dialog-title]',
		dialogSelectedTime: '[data-js-fitting-form-selected-date]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(form) {
		super(form);

		this.phoneControl = this.form.querySelector('[data-js-input-mask="phone"]');
		this.submitButton = this.form.querySelector(this.selectors.submitButton);
		this.errorsElement = this.form.querySelector(this.selectors.errorsElement);
		this.closestDialog = this.form.closest(this.selectors.closestDialog);
		this.dialogTitle = this.closestDialog?.querySelector(
			this.selectors.dialogTitle
		);
		this.dialogSelectedTime = this.closestDialog.querySelector(
			this.selectors.dialogSelectedTime
		);

		this.fields = this.findFields();
		this.state = this.setupFormState();

		console.log({ ...this.state });

		this.prevState = { ...this.state };

		this.changeFormHandler = this.changeFormHandler.bind(this);
		this.submitForm = this.submitForm.bind(this);
		this.closeDialogHandler = this.closeDialogHandler.bind(this);
		this.openDialogHandler = this.openDialogHandler.bind(this);

		this.bindEvents();
	}

	_normalizeStep(stepNumber) {
		if (stepNumber < 0) {
			return 0;
		}

		if (stepNumber >= this.steps.length) {
			return this.steps.length - 1;
		}

		return stepNumber;
	}

	findFields() {
		return Array.from(this.form.elements).filter((element) => element.name);
	}

	setupFormState() {
		const state = {
			dialogMessage: 'Запись на примерку',
			dress_category: null,
			isUpdatingSlots: false,
			isSubmitting: false,
		};

		for (const element of this.fields) {
			const { name, value, type } = element;

			if (name.endsWith('[]')) {
				const elementName = name.slice(0, -2);
				state[elementName] = [];

				if (type === 'checkbox') {
					if (element.checked) {
						state[elementName].push(value);
					}
					continue;
				}
			} else {
				if (['checkbox', 'radio'].includes(type)) {
					if (element.checked) {
						state[name] = value ?? true;
						continue;
					} else {
						state[name] = null;
						continue;
					}
				}

				state[name] = value;
			}
		}

		return this._getProxyState(state);
	}

	/**
	 * IMask safari change event fix
	 */
	phoneControlBlurHandler = (event) => {
		event.target.dispatchEvent(
			new Event('change', {
				bubbles: true,
			})
		);
	};

	checkboxChangeHandler(event) {
		const { target } = event;
		const { name, value, checked } = target;

		if (name.endsWith('[]')) {
			const fieldName = name.slice(0, -2);

			if (checked) {
				this.state[fieldName] = [...this.state[fieldName], value];
			} else {
				this.state[fieldName] = this.state[fieldName].filter(
					(item) => item !== value
				);
			}
		} else {
			if (checked) {
				this.state[name] = value?.trim() ?? true;
			} else {
				this.state[name] = false;
			}
		}
	}

	/**
	 *
	 * @param {ChangeEvent} event
	 */
	changeFormHandler = (event) => {
		const { target } = event;
		const { name, value, type } = target;

		if (type === 'checkbox') {
			this.checkboxChangeHandler(event);
			return;
		}

		this.state[name] = value?.trim();
	};

	inputFormHandler = (event) => {
		const { target } = event;
		const { name, value, type } = target;

		if (['checkbox', 'radio'].includes(type)) {
			return;
		}

		this.state[name] = value.trim();
	};

	async getNewTimeSlots() {
		try {
			const neededFields = ['date', 'fitting_type[]'];
			const formData = new FormData(this.form);
			const keys = Array.from(formData.keys());

			for (const key of keys) {
				if (!neededFields.includes(key)) {
					formData.delete(key);
				}
			}

			formData.append('action', 'get_fitting_time_slots');
			formData.append('nonce', LOVE_FOREVER.NONCE);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			// console.log({ body });

			if (!body.success) {
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	async updateTimeSlots() {
		if (this.state.isUpdatingSlots) return;

		this.state.isUpdatingSlots = true;

		const { data, error } = await promiseWrapper(this.getNewTimeSlots());

		console.log({ data, error });

		if (error) {
			this.state.success = false;
			this.state.error = error.message;
			this.state.isUpdatingSlots = false;
			return;
		}

		/**
		 * @type {HTMLSelectElement}
		 */
		const timeSelectControl = this.form.elements.time;

		timeSelectControl.innerHTML = '';

		for (const time in data.slots) {
			if (Object.prototype.hasOwnProperty.call(data.slots, time)) {
				const slot = data.slots[time];
				const option = document.createElement('option');

				option.value = time;
				option.disabled = data.disableSlots && slot.available_for_booking === 0;

				let optionContent = time;

				if (!data.disableSlots) {
					optionContent += ` (${slot.available_for_booking} из ${slot.max_fittings})`;
				}

				option.textContent = optionContent;

				timeSelectControl.append(option);
			}
		}

		this.state.isUpdatingSlots = false;
	}

	async submitForm(event) {
		event.preventDefault();

		if (this.state.isSubmitting) {
			return;
		}

		this.state.isSubmitting = true;
		this.state.error = null;

		try {
			const formData = new FormData(this.form);
			formData.append('action', 'create_new_fitting_record');

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			console.log({ body });

			this.state.success = body.success;

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			this.state.dialogMessage = body.data.message;

			document.dispatchEvent(
				new Event('updateFittings', {
					bubbles: true,
					cancelable: true,
				})
			);
		} catch (error) {
			console.error(error);
			this.state.error = error.message;
		} finally {
			this.state.isSubmitting = false;
		}
	}

	closeDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.reset();
		}
	}

	openDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.updateTimeSlots();
		}
	}

	reset() {
		this.form.reset();
		this.state = this.setupFormState();
		this.form.dispatchEvent(new Event('change'));
	}

	updateUI() {
		console.log({ ...this.state });

		if (
			this.prevState.date !== this.state.date ||
			this.prevState.fitting_type !== this.state.fitting_type
		) {
			this.updateTimeSlots();
		}

		if (
			this.prevState.isUpdatingSlots !== this.state.isUpdatingSlots &&
			!this.state.isUpdatingSlots
		) {
			if ($(this.form.elements.time).data('ui-selectmenu')) {
				$(this.form.elements.time).selectmenu('refresh');
				$(this.form.elements.time).selectmenu('enable');
			}
			this.form.elements.time.dispatchEvent(
				new Event('change', {
					bubbles: true,
					cancelable: true,
				})
			);
		}

		this.errorsElement.innerHTML = `<p>${this.state.error}</p>`;
		this.errorsElement.hidden = !Boolean(this.state.error);

		const allFieldChecked =
			isValidRussianPhone(this.state.phone) &&
			this.state.name &&
			this.state.date &&
			this.state.time;

		this.submitButton.disabled = !allFieldChecked || this.state.isSubmitting;

		if (this.dialogTitle) {
			this.dialogTitle.textContent = this.state.dialogMessage;
		}

		this.form.hidden = this.state.success;

		if (this.closestDialog) {
			this.form.nextElementSibling.hidden = !this.state.success;
			this.form.nextElementSibling.disabled = !this.state.success;
		}

		this.prevState = { ...this.state };
	}

	bindEvents() {
		if (this.phoneControl) {
			this.phoneControl.addEventListener('blur', this.phoneControlBlurHandler);
		}
		this.form.addEventListener('change', this.changeFormHandler);
		this.form.addEventListener('submit', this.submitForm);
		this.form.addEventListener('input', this.inputFormHandler);
		document.addEventListener('dialogClose', this.closeDialogHandler);
		document.addEventListener('dialogOpen', this.openDialogHandler);
	}

	destroy() {
		if (this.phoneControl) {
			this.phoneControl.removeEventListener(
				'blur',
				this.phoneControlBlurHandler
			);
		}
		this.form.removeEventListener('change', this.changeFormHandler);
		this.form.removeEventListener('submit', this.submitForm);
		document.removeEventListener('dialogClose', this.closeDialogHandler);
		document.removeEventListener('dialogOpen', this.openDialogHandler);
	}
}

class SingleFittingForm extends BaseFittingForm {
	selectors = {
		openDialogButton: '[data-js-fitting-form-dialog-button]',
		errorsElement: '[data-js-fitting-form-errors]',
		closestDialog: '[data-js-dialog]',
		dialogTitle: '[data-js-dialog-title]',
		dialogFormWrapper: '[data-js-fitting-form-wrapper]',
		dialogSelectedTime: '[data-js-fitting-form-selected-date]',
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(form) {
		super(form);
		this.timeControlWrapper = this.form.elements.time.parentElement;
		this.openDialogButton = this.form.querySelector(
			this.selectors.openDialogButton
		);
		this.dialog = document.getElementById(
			this.openDialogButton.dataset.jsDialogOpenButton
		);
		this.dialogTitle = this.dialog.querySelector(this.selectors.dialogTitle);
		this.errorsElement = this.dialog.querySelector(
			this.selectors.errorsElement
		);
		this.dialogSelectedTime = this.dialog.querySelector(
			this.selectors.dialogSelectedTime
		);
		this.submitButton = this.dialog.querySelector(
			`button[type="submit"][form="${this.form.id}"]`
		);
		this.dialogFormWrapper = this.dialog.querySelector(
			this.selectors.dialogFormWrapper
		);
		this.successCloseDialogButton = this.dialogFormWrapper.nextElementSibling;

		this.state = this._getProxyState({
			...this.state,
			target_dress: this.form.elements.target_dress.value,
			client_favorite_dresses:
				this.form.elements.client_favorite_dresses?.value,
			submit_fitting_form_nonce:
				this.form.elements.submit_fitting_form_nonce.value,
			dialogMessage: 'Запись на примерку',
			isUpdatingSlots: false,
			isSubmitted: false,
		});

		this.prevState = { ...this.state };

		this.dialogSelectedTime.textContent = formatDateToRussian(
			`${this.state.date} ${this.state.time}`
		);

		this.bindEvents();
	}

	reset() {
		this.form.reset();
		this.state.success = false;
		this.state.dialogMessage = 'Запись на примерку';
		this.state.step = 0;
		this.state.dateIncrementRatio = 0;
		this.state.isSubmitted = false;
		this.form.dispatchEvent(new Event('change'));
	}

	/**
	 *
	 * @param {ChangeEvent} event
	 */
	onChange = (event) => {
		console.log({ event });

		this.state.error = null;

		const formData = Object.fromEntries(new FormData(this.form));

		for (const name in formData) {
			if (Object.prototype.hasOwnProperty.call(formData, name)) {
				const value = formData[name];

				this.state[name] = value;
			}
		}
	};

	/**
	 *
	 * @param {SubmitEvent} event
	 */
	submitForm = async (event) => {
		event.preventDefault();

		if (this.state.isSubmitting) {
			return;
		}

		this.state.isSubmitting = true;
		this.state.error = null;

		try {
			const formData = new FormData(this.form);
			formData.append('action', 'create_new_fitting_record');

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			// console.log(body);

			this.state.success = body.success;

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			this.state.dialogMessage = body.data.message;
			this.state.dateIncrementRatio = 1;
			this.state.isSubmitted = true;
			this.loadTimeSlots();
		} catch (error) {
			console.error(error);
			this.state.error = error.message;
		} finally {
			this.state.isSubmitting = false;
		}
	};

	/**
	 *
	 * @param {CustomEvent} event
	 */
	onCloseDialog = (event) => {
		if (event.detail.dialogId === this.dialog.id && this.state.isSubmitted) {
			this.reset();
		}
	};

	async loadTimeSlots() {
		const selectedDate = this.form.elements.date.value;

		if (!selectedDate) return;

		this.state.isUpdatingSlots = true;

		try {
			const formData = new FormData();
			formData.append('action', 'get_fitting_time_slots');
			formData.append('nonce', LOVE_FOREVER.NONCE);
			formData.append('date', selectedDate);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			console.log(body);

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			const timeSelectControl = this.form.elements.time;

			timeSelectControl.innerHTML = '';
			// let timeOptions = '';
			let newSelectedOption = null;

			for (const time in body.data.slots) {
				const slot = body.data.slots[time];
				const option = document.createElement('option');

				option.value = time;
				option.textContent = time;
				option.disabled = slot.available === 0;

				if (newSelectedOption === null && slot.available > 0) {
					option.selected = true;
					newSelectedOption = option;
				}

				timeSelectControl.append(option);
			}

			timeSelectControl.dispatchEvent(
				new Event('change', {
					bubbles: true,
				})
			);
		} catch (error) {
			console.error(error);
			alert(error.message);
		} finally {
			this.state.isUpdatingSlots = false;
		}
	}

	updateUI() {
		this.timeControlWrapper.classList.toggle(
			this.stateSelectors.isLoading,
			this.state.isUpdatingSlots
		);

		if ($(this.form.elements.time).selectmenu('instance') !== undefined) {
			if (this.state.isUpdatingSlots) {
				$(this.form.elements.time).selectmenu('disable');
			}

			if (
				this.prevState.isUpdatingSlots !== this.state.isUpdatingSlots &&
				!this.state.isUpdatingSlots
			) {
				$(this.form.elements.time).selectmenu('enable');
				$(this.form.elements.time).selectmenu('refresh');
				this.form.elements.time.dispatchEvent(
					new Event('change', {
						bubbles: true,
						cancelable: true,
					})
				);
			}
		}

		if (
			this.state.date !== this.prevState.date &&
			!this.state.isUpdatingSlots
		) {
			this.state.isUpdatingSlots = true;
			this.loadTimeSlots();
		}

		if (this.state.date && this.state.time) {
			this.dialogSelectedTime.textContent = formatDateToRussian(
				`${this.state.date} ${this.state.time}`
			);
		} else {
			this.dialogSelectedTime.textContent = '';
		}

		this.errorsElement.innerHTML = `<p>${this.state.error}</p>`;
		this.errorsElement.hidden = !Boolean(this.state.error);

		this.submitButton.disabled = this.state.isSubmitting;

		if (this.dialogTitle) {
			this.dialogTitle.textContent = this.state.dialogMessage;
		}

		this.dialogFormWrapper.hidden = this.state.success;
		this.successCloseDialogButton.hidden = !this.state.success;
		this.successCloseDialogButton.disabled = !this.state.success;

		this.prevState = { ...this.state };
	}

	bindEvents() {
		this.form.addEventListener('change', this.onChange);
		this.form.addEventListener('submit', this.submitForm);
		document.addEventListener('dialogClose', this.onCloseDialog);
	}

	destroy() {
		this.form.removeEventListener('change', this.onChange);
		this.form.removeEventListener('submit', this.submitForm);
		document.removeEventListener('dialogClose', this.onCloseDialog);
	}
}

class EditFittingForm extends BaseFittingForm {
	selectors = {
		root: ROOT_SELECTOR,
		errorElement: '[data-js-fitting-form-error]',
	};
	/**
	 *
	 * @param {HTMLFormElement} form
	 */
	constructor(form) {
		super(form);

		this.submitButton = this.form.querySelector('[type="submit"]');
		this.errorElement = this.form.querySelector(this.selectors.errorElement);

		const state = { ...this.state };
		const formData = Object.fromEntries(new FormData(this.form));

		for (const name in formData) {
			if (Object.prototype.hasOwnProperty.call(formData, name)) {
				const value = formData[name];
				state[name] = value;
			}
		}

		this.state = this._getProxyState({
			...state,
			isUpdatingSlots: false,
		});

		this.prevState = { ...this.state };

		this.bindEvents();
	}

	async updateFitting() {
		try {
			const formData = new FormData(this.form);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			// console.log(body);

			if (!body.success) {
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	async getNewTimeSlots() {
		try {
			const formData = new FormData();

			formData.append('action', 'get_fitting_time_slots');
			formData.append('nonce', LOVE_FOREVER.NONCE);
			formData.append('date', this.state.date);
			formData.append('fitting-id', this.form.elements['fitting-id']);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			if (!body.success) {
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	async updateTimeSlots() {
		if (this.state.isUpdatingSlots) return;

		this.state.isUpdatingSlots = true;

		await wait(2000);

		const { data, error } = await promiseWrapper(this.getNewTimeSlots());

		if (error) {
			this.state.success = false;
			this.state.error = error.message;
			this.state.isUpdatingSlots = false;
			return;
		}

		const timeSelectControl = this.form.elements.time;
		let timeOptions = '';

		for (const time in data.slots) {
			if (Object.prototype.hasOwnProperty.call(data.slots, time)) {
				const slot = data.slots[time];

				timeOptions += `<option value="${time}" ${
					slot.available === 0 ? 'disabled' : ''
				}>${time} (Доступно примерок: ${slot.available})</option>`;
			}
		}

		timeSelectControl.innerHTML = timeOptions;
		this.state.isUpdatingSlots = false;
	}

	updateState() {
		const formData = Object.fromEntries(new FormData(this.form));

		for (const name in formData) {
			if (Object.prototype.hasOwnProperty.call(formData, name)) {
				const value = formData[name];
				this.state[name] = value;
			}
		}
	}

	/**
	 *
	 * @param {Event} event
	 */
	onChange = (event) => {
		this.state.error = null;

		this.updateState();
	};

	/**
	 *
	 * @param {SubmitEvent} event
	 */
	submitForm = async (event) => {
		event.preventDefault();

		if (this.state.isSubmitting || event.target !== this.form) return;

		this.state.isSubmitting = true;
		this.state.error = null;

		const { data, error } = await promiseWrapper(this.updateFitting());

		this.state.isSubmitting = false;

		if (error) {
			this.state.success = false;
			this.state.error = error.message;
		} else {
			this.state.success = true;
			window.location.assign(
				`${window.location.origin}${window.location.pathname}?updated=true`
			);
		}
	};

	updateUI() {
		if (this.prevState.date !== this.state.date) {
			this.updateTimeSlots();
		}

		if (this.state.isUpdatingSlots) {
			$(this.form.elements.time).selectmenu('disable');
		}

		if (
			this.prevState.isUpdatingSlots !== this.state.isUpdatingSlots &&
			!this.state.isUpdatingSlots
		) {
			$(this.form.elements.time).selectmenu('refresh');
			$(this.form.elements.time).selectmenu('enable');
			this.form.elements.time.dispatchEvent(
				new Event('change', {
					bubbles: true,
					cancelable: true,
				})
			);
		}

		this.submitButton.disabled =
			this.state.isSubmitting || this.state.isUpdatingSlots;

		this.submitButton.textContent = this.state.isSubmitting
			? 'Обновляем...'
			: 'Обновить';

		this.errorElement.innerHTML = this.state.error
			? `<div class="alert alert--error"><p>${this.state.error}</p></div>`
			: '';

		this.prevState = { ...this.state };
	}

	bindEvents() {
		document.addEventListener('submit', this.submitForm);
		this.form.addEventListener('change', this.onChange);
	}

	destroy() {
		document.removeEventListener('submit', this.submitForm);
		this.form.removeEventListener('change', this.onChange);
	}
}

class FittingFormCollection {
	/**
	 * @type {Map<string, BaseFittingForm>}
	 */
	static forms = new Map();

	static getFittingFormById(id) {
		return this.forms.get(id);
	}

	static destroyAll() {
		this.forms.forEach((fittingForm, id) => {
			fittingForm.destroy();
		});
		this.forms.clear();
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((fittingForm) => {
			let fittingFormInstance = null;
			// let fittingFormInstance = new FittingForm(fittingForm);

			switch (fittingForm.id) {
				case 'singleDressForm':
					fittingFormInstance = new SingleFittingForm(fittingForm);
					break;
				case 'editFittingForm':
					fittingFormInstance = new EditFittingForm(fittingForm);
					break;
				case 'globalDressFittingFormSimpler':
					fittingFormInstance = new GlobalFittingFormSimpler(fittingForm);
					break;
				default:
					fittingFormInstance = new GlobalFittingForm(fittingForm);
					break;
			}

			this.forms.set(fittingForm.id, fittingFormInstance);
		});
	}
}

export { FittingFormCollection };
