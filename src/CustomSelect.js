import classNames from 'classnames';
import MatchMedia from './MatchMedia';
const ROOT_SELECTOR = '[data-js-custom-select]';

class CustomSelect {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.root = element;
		this.optionsAttr = element.dataset.jsCustomSelect
			? JSON.parse(element.dataset.jsCustomSelect)
			: {};

		this.selectOptions = {
			hasBorder: true,
			type: 'select',
			...this.optionsAttr,
		};

		this.toogleSelectDeviceType();

		this.bindEvents();
	}

	init() {
		$(this.root).selectmenu({
			width: false,
			position: this.getDropdownPosition(),
			classes: {
				'ui-selectmenu-button': classNames('loveforever-select', {
					'loveforever-select--no-border': !this.selectOptions.hasBorder,
				}),
				'ui-selectmenu-button-open': 'is-active',
				'ui-selectmenu-text': 'loveforever-select__value',
				'ui-selectmenu-icon': 'loveforever-select__icon',
				'ui-selectmenu-menu': `loveforever-select__menu loveforever-select__menu--${this.selectOptions.type}`,
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

	getDropdownPosition(type = 'select') {
		switch (type) {
			case 'time':
				return {
					my: 'right top+10rem',
					at: 'right bottom',
				};
			default:
				return {
					my: 'left top',
					at: 'left-5rem bottom',
				};
		}
	}

	toogleSelectDeviceType(isMobile = MatchMedia.mobile.matches) {
		console.log({ isMobile });

		!isMobile ? this.init() : this.destroy();
	}

	bindEvents() {
		MatchMedia.mobile.addEventListener('change', (event) => {
			this.toogleSelectDeviceType(event.matches);
		});
	}

	destroy() {
		if (!$(this.root).selectmenu('instance')) {
			return;
		}

		$(this.root).selectmenu('destroy');
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
