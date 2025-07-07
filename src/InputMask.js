import IMask from 'imask';
const ROOT_SELECTOR = '[data-js-input-mask]';

class InputMask {
	configs = {
		phone: {
			mask: [
				{
					mask: '8 (000) 000-00-00',
					startsWith: '8',
				},
				{
					mask: '+{7} (000) 000-00-00',
					startsWith: '7',
				},
				{
					// fallback: +7 маска, но префикс добавим вручную
					mask: '+{7} (000) 000-00-00',
					startsWith: '',
					isDefault: true,
				},
			],
			dispatch: function (appended, dynamicMasked) {
				const rawInput = (dynamicMasked.value + appended).replace(/\D/g, '');
				const firstDigit = rawInput.charAt(0);

				if (firstDigit === '8') {
					return dynamicMasked.compiledMasks.find((m) => m.startsWith === '8');
				}

				if (firstDigit === '7') {
					return dynamicMasked.compiledMasks.find((m) => m.startsWith === '7');
				}

				// Любая другая цифра → +7 маска
				return dynamicMasked.compiledMasks.find((m) => m.isDefault);
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
