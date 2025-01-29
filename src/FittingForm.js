const ROOT_SELECTOR = '[data-js-fitting-form]';

class FittingForm {
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
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor(fittingForm, initialState = {}) {
		console.log('FittingForm');

		this.fittingForm = fittingForm;
		this.steps = this.fittingForm.querySelectorAll(this.selectors.step);
		this.backButton = this.fittingForm.querySelector(this.selectors.backButton);
		this.nextButton = this.fittingForm.querySelector(this.selectors.nextButton);
		this.slotsContainer = this.fittingForm.querySelector(
			this.selectors.slotsContainer
		);
		this.prevSlotsButton = this.fittingForm.querySelector(
			this.selectors.prevSlotsButton
		);
		this.nextSlotsButton = this.fittingForm.querySelector(
			this.selectors.nextSlotsButton
		);
		this.submitButton = this.fittingForm.querySelector(
			this.selectors.submitButton
		);
		this.errorsElement = this.fittingForm.querySelector(
			this.selectors.errorsElement
		);
		this.closestDialog = this.fittingForm.closest(this.selectors.closestDialog);
		this.dialogTitle = this.closestDialog?.querySelector(
			this.selectors.dialogTitle
		);

		this.state = this._getProxyState({
			dateIncrementRatio: 0,
			dialogMessage: 'Запись на примерку',
			success: false,
			error: null,
			step: 0,
			dress_category: null,
			date: null,
			time: null,
			name: '',
			phone: '',
			isUpdatingSlots: false,
			isSubmitting: false,
			...initialState,
		});

		this.prevState = { ...this.state };

		this.changeFormHandler = this.changeFormHandler.bind(this);
		this.submitFormHandler = this.submitFormHandler.bind(this);
		this.closeDialogHandler = this.closeDialogHandler.bind(this);
		this.openDialogHandler = this.openDialogHandler.bind(this);
		this.prevStep = this.prevStep.bind(this);
		this.prevDates = this.prevDates.bind(this);
		this.nextDates = this.nextDates.bind(this);

		this.bindEvents();
	}

	_getProxyState(initialState) {
		return new Proxy(initialState, {
			get: (target, prop) => {
				return target[prop];
			},
			set: (target, prop, newValue) => {
				const currentValue = target[prop];

				target[prop] = newValue;

				if (currentValue !== newValue) {
					this.updateUI();
				}

				return true;
			},
		});
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

	changeFormHandler(event) {
		this.state.error = null;

		const { target } = event;

		if (target.dataset?.jsFittingFormDateValue) {
			this.fittingForm.querySelector(this.selectors.dateInput).value =
				target.dataset.jsFittingFormDateValue;
		}

		const formState = Object.fromEntries(new FormData(this.fittingForm));

		for (const key in formState) {
			if (Object.prototype.hasOwnProperty.call(formState, key)) {
				const value = formState[key];
				this.state[key] = value;
			}
		}
	}

	getDateTimeSlots(dateIncrementRatio) {
		if (this.state.isUpdatingSlots) {
			return;
		}

		this.state.isUpdatingSlots = true;

		const formData = new FormData();

		formData.append('action', 'get_date_time_slots');
		formData.append('nonce', LOVE_FOREVER.NONCE);
		formData.append('date-increment-ratio', dateIncrementRatio);

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

	submitFormHandler(event) {
		event.preventDefault();

		if (this.state.isSubmitting) {
			return;
		}

		this.state.isSubmitting = true;
		this.state.error = null;

		const formData = new FormData(this.fittingForm);

		formData.append('action', 'create_new_fitting_record');

		fetch(LOVE_FOREVER.AJAX_URL, {
			method: 'POST',
			body: formData,
		})
			.then((response) => response.json())
			.then(({ success, data }) => {
				this.state.success = success;

				if (!success) {
					throw new Error(data.message);
				}

				this.state.dialogMessage = data.message;
				this.state.dateIncrementRatio = 1;
			})
			.catch((error) => {
				this.state.error = error.message;
			})
			.finally(() => {
				this.state.isSubmitting = false;
			});
	}

	closeDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.reset();
		}
	}

	openDialogHandler(event) {
		if (event.detail.dialogId === this.closestDialog?.id) {
			this.getDateTimeSlots(this.state.dateIncrementRatio);
		}
	}

	reset() {
		this.fittingForm.reset();
		this.fittingForm.dispatchEvent(new Event('change'));
		this.state.success = false;
		this.state.dialogMessage = 'Запись на примерку';
		this.state.step = 0;
		this.state.dateIncrementRatio = 0;
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

		if (this.prevState.time !== this.state.time) {
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
			this.getDateTimeSlots(this.state.dateIncrementRatio);
		}

		this.errorsElement.innerHTML = `<p>${this.state.error}</p>`;
		this.errorsElement.hidden = !Boolean(this.state.error);

		this.submitButton.disabled = this.state.isSubmitting;

		if (this.dialogTitle) {
			this.dialogTitle.textContent = this.state.dialogMessage;
		}

		this.fittingForm.hidden = this.state.success;

		if (this.closestDialog) {
			this.fittingForm.nextElementSibling.hidden = !this.state.success;
			this.fittingForm.nextElementSibling.disabled = !this.state.success;
		}

		this.prevState = { ...this.state };
	}

	bindEvents() {
		this.fittingForm.addEventListener('change', this.changeFormHandler);
		this.fittingForm.addEventListener('submit', this.submitFormHandler);
		this.backButton.addEventListener('click', this.prevStep);
		this.prevSlotsButton.addEventListener('click', this.prevDates);
		this.nextSlotsButton.addEventListener('click', this.nextDates);
		document.addEventListener('dialogClose', this.closeDialogHandler);
		document.addEventListener('dialogOpen', this.openDialogHandler);
	}

	destroy() {
		this.fittingForm.removeEventListener('change', this.changeFormHandler);
		this.fittingForm.removeEventListener('submit', this.submitFormHandler);
		this.backButton.removeEventListener('click', this.prevStep);
		this.prevSlotsButton.removeEventListener('click', this.prevDates);
		this.nextSlotsButton.removeEventListener('click', this.nextDates);
		document.removeEventListener('dialogClose', this.closeDialogHandler);
		document.removeEventListener('dialogOpen', this.openDialogHandler);
	}
}

class FittingFormCollection {
	/**
	 * @type {Map<string, FittingForm>}
	 */
	static fittingForms = new Map();

	static getFittingFormById(id) {
		return this.fittingForms.get(id);
	}

	static destroyAll() {
		this.fittingForms.forEach((fittingForm, id) => {
			fittingForm.destroy();
			this.fittingForms.delete(id);
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((fittingForm) => {
			const formInstance = new FittingForm(fittingForm);
			this.fittingForms.set(fittingForm.id, formInstance);
		});
	}
}

export { FittingForm, FittingFormCollection };
