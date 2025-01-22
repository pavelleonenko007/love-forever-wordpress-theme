import IMask from 'imask';
const ROOT_SELECTOR = '[data-js-input-mask]';

class InputMask {
	constructor(inputField) {
		this.rootElement = inputField;
		this.init();
	}

	init() {
		IMask(this.rootElement, {
			mask: this.rootElement.dataset.jsInputMask,
		});
	}
}

class InputMaskCollection {
	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((input) => {
			new InputMask(input);
		});
	}
}

export default InputMaskCollection;
