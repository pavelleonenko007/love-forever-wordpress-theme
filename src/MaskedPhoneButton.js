export default class MaskedPhoneButtonCollection {
	selectors = {
		phone: '[data-js-phone-number]',
		button: '[data-js-phone-number-button]',
	};

	stateSelectors = {
		isActive: 'is-active',
	};

	constructor() {
		this.bindEvents();
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick = (event) => {
		if (event.target.closest(this.selectors.button)) {
			event.preventDefault();
			event.stopPropagation();

			const button = event.target.closest(this.selectors.button);
			const phoneElementId = button.dataset.jsPhoneNumberButton;			

			if (!phoneElementId) return;

			const phoneElement = document.getElementById(
				button.dataset.jsPhoneNumberButton
			);

			if (!phoneElement) return;

			phoneElement.textContent = phoneElement.dataset.jsPhoneNumber;

			button.classList.add(this.stateSelectors.isActive);
		}
	};

	bindEvents() {
		document.addEventListener('click', this.onClick);
	}
}
