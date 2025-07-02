import IMask from 'imask';
const ROOT_SELECTOR = '[data-js-input-mask]';

class InputMask {
	configs = {
		phone: {
			mask: [
				{
					mask: '+{7} (000) 000-00-00',
					startsWith: '7',
					lazy: false,
					placeholderChar: '_',
				},
				{
					mask: '8 (000) 000-00-00',
					startsWith: '8',
					lazy: false,
					placeholderChar: '_',
				},
			],
			dispatch: function (appended, dynamicMasked) {
				const inputValue = (dynamicMasked.value + appended).replace(/\D/g, '');

				return dynamicMasked.compiledMasks.find((m) =>
					inputValue.startsWith(m.startsWith)
				);
			},
		},
	};

	constructor(inputField) {
		this.rootElement = inputField;
		this.config = this.configs[this.rootElement.dataset.jsInputMask];
		this.imask = IMask(this.rootElement, this.config);
	}

	validate() {
		return this.imask.masked.isComplete;
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
