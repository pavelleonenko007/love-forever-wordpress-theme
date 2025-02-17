import BaseComponent from './BaseComponent';
import { formatDateToRussian, promiseWrapper, wait } from './utils';

const ROOT_SELECTOR = '[data-js-fitting-form]';

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
			date: this.form.elements.date.value,
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
			client_favorite_dresses: this.form.elements.client_favorite_dresses.value,
			submit_fitting_form_nonce:
				this.form.elements.submit_fitting_form_nonce.value,
			dialogMessage: 'Запись на примерку',
			isUpdatingSlots: false,
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
		this.form.dispatchEvent(new Event('change'));
	}

	/**
	 *
	 * @param {ChangeEvent} event
	 */
	onChange = (event) => {
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
		if (event.detail.dialogId === this.dialog.id) {
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

			// console.log(body);

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			const timeSelectControl = this.form.elements.time;
			let timeOptions = '';

			for (const time in body.data.slots) {
				if (Object.prototype.hasOwnProperty.call(body.data.slots, time)) {
					const slot = body.data.slots[time];

					timeOptions += `<option value="${time}" ${
						slot.available === 0 ? 'disabled' : ''
					}>${time}</option>`;
				}
			}

			timeSelectControl.innerHTML = timeOptions;
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

		console.log({ data, error });

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
		console.log({ ...this.state });

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

			switch (fittingForm.id) {
				case 'singleDressForm':
					fittingFormInstance = new SingleFittingForm(fittingForm);
					break;
				case 'editFittingForm':
					fittingFormInstance = new EditFittingForm(fittingForm);
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
