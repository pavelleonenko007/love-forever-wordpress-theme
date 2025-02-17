export default class FormsValidator {
	selectors = {
		form: '[data-js-form]',
		fieldErrors: '[data-js-form-field-errors]',
	};

	errorMessages = {
		valueMissing: () => 'Пожалуйста, заполните это поле',
		patternMismatch: ({ title }) => title || 'Данные не соответствуют формату',
		tooShort: ({ minLength }) =>
			`Слишком короткое значение, минимум символов — ${minLength}`,
		tooLong: ({ maxLength }) =>
			`Слишком длинное значение, ограничение символов — ${maxLength}`,
	};

	constructor() {
		this.bindEvents();
	}

	manageErrors(fieldControlElement, errorMessages) {
		const fieldErrorsElement = ['radio', 'checkbox'].includes(
			fieldControlElement.type
		)
			? fieldControlElement.parentElement.parentElement.querySelector(
					this.selectors.fieldErrors
			  )
			: fieldControlElement.parentElement.querySelector(
					this.selectors.fieldErrors
			  );

		console.log({ fieldErrorsElement, fieldControlElement, errorMessages });

		fieldErrorsElement.innerHTML = errorMessages
			.map((message) => `<span class="field__error">${message}</span>`)
			.join('');
	}

	validateField(fieldControlElement) {
		if (
			fieldControlElement.type === 'checkbox' &&
			fieldControlElement.name.endsWith('[]')
		) {
			const form = fieldControlElement.closest('form');
			const checkboxes = form.querySelectorAll(
				`input[name="${fieldControlElement.name}"]`
			);
			const isValid = Array.from(checkboxes).some(
				(checkbox) => checkbox.checked
			);

			this.manageErrors(
				fieldControlElement,
				isValid ? [] : [this.errorMessages.valueMissing()]
			);
			fieldControlElement.ariaInvalid = !isValid;
			return isValid;
		}

		const errors = fieldControlElement.validity;
		const errorMessages = [];

		Object.entries(this.errorMessages).forEach(
			([errorType, getErrorMessage]) => {
				if (errors[errorType]) {
					errorMessages.push(getErrorMessage(fieldControlElement));
				}
			}
		);

		this.manageErrors(fieldControlElement, errorMessages);

		const isValid = errorMessages.length === 0;

		fieldControlElement.ariaInvalid = !isValid;

		return isValid;
	}

	onBlur(event) {
		const { target } = event;
		const isFormField = target.closest(this.selectors.form);
		const isRequired = target.required;

		if (isFormField && isRequired) {
			this.validateField(target);
		}
	}

	onChange(event) {
		const { target } = event;
		const isRequired = target.required;
		const isToggleType = ['radio', 'checkbox'].includes(target.type);

		if (isToggleType && isRequired) {
			this.validateField(target);
		}
	}

	/**
	 *
	 * @param {Event} event
	 */
	onSubmit(event) {
		const isFormElement = event.target.matches(this.selectors.form);
		if (!isFormElement) {
			return;
		}

		const requiredControlElements = [...event.target.elements].filter(
			({ required }) => required
		);
		let isFormValid = true;
		let firstInvalidFieldControl = null;

		requiredControlElements.forEach((element) => {
			const isFieldValid = this.validateField(element);

			if (!isFieldValid) {
				isFormValid = false;

				if (!firstInvalidFieldControl) {
					firstInvalidFieldControl = element;
				}
			}
		});

		if (!isFormValid) {
			event.preventDefault();
			event.stopImmediatePropagation();
			firstInvalidFieldControl.focus();
		}
	}

	bindEvents() {
		document.addEventListener(
			'blur',
			(event) => {
				this.onBlur(event);
			},
			{ capture: true }
		);
		document.addEventListener('change', (event) => this.onChange(event));
		document.addEventListener('submit', (event) => this.onSubmit(event));
	}
}
