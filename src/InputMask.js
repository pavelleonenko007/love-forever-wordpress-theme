import IMask from 'imask';
const ROOT_SELECTOR = '[data-js-input-mask]';

class InputMask {
	constructor(inputField) {
		this.rootElement = inputField;
		this.imask = IMask(this.rootElement, {
			mask: this.rootElement.dataset.jsInputMask,
		});
	}

	destroy() {
		this.imask.destroy();
	}
}

class InputMaskCollection {
	/**
	 * @type {Map<string, InputMask>}
	 */
	static inputMaskFields = new Map();

	static destroyAll() {
		this.inputMaskFields.forEach((inputMask, id) => {
			inputMask.destroy();
			this.inputMaskFields.delete(id);
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((input) => {
			const inputMask = new InputMask(input);
			this.inputMaskFields.set(input.id, inputMask);
		});
	}
}

export default InputMaskCollection;
