const ROOT_SELECTOR = '[data-js-custom-select]';

class CustomSelect {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.select = $(element).selectmenu({
			classes: {
				'ui-selectmenu-button': 'loveforever-select',
				'ui-selectmenu-button-open': 'is-active',
				'ui-selectmenu-text': 'loveforever-select__value',
				'ui-selectmenu-icon': 'loveforever-select__icon',
				'ui-selectmenu-menu': 'loveforever-select__menu',
				'ui-selectmenu-open': 'is-active',
				'ui-selectmenu-disabled': 'is-disabled',
			},
			change: (event, ui) => {
			},
		});
	}

	destroy() {
		this.select.selectmenu('destroy');
	}
}

export default class CustomSelectCollection {
	/**
	 * @type {Map<HTMLElement, CustomSelect>}
	 */
	static customSelects = new Map();
	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			this.customSelects.set(element, new CustomSelect(element));
		});
	}
	static destroyAll() {
		this.customSelects.forEach((instance) => {
			instance.destroy();
		});
		this.customSelects.clear();
	}
}
