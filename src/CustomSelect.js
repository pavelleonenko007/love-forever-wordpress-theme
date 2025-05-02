import classNames from 'classnames';
const ROOT_SELECTOR = '[data-js-custom-select]';

class CustomSelect {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.optionsAttr = element.dataset.jsCustomSelect
			? JSON.parse(element.dataset.jsCustomSelect)
			: {};

		this.selectOptions = {
			hasBorder: true,
			...this.optionsAttr,
		};

		this.select = $(element).selectmenu({
			width: false,
			position: { at: 'left-20 bottom+15rem' },
			classes: {
				'ui-selectmenu-button': classNames('loveforever-select', {
					'loveforever-select--no-border': !this.selectOptions.hasBorder,
				}),
				'ui-selectmenu-button-open': 'is-active',
				'ui-selectmenu-text': 'loveforever-select__value',
				'ui-selectmenu-icon': 'loveforever-select__icon',
				'ui-selectmenu-menu': 'loveforever-select__menu',
				'ui-selectmenu-open': 'is-active',
				'ui-selectmenu-disabled': 'is-disabled',
			},
			change: (event, ui) => {
				console.log({ event, ui });
				event.target.dispatchEvent(
					new Event('change', {
						bubbles: true,
					})
				);
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
