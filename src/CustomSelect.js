import classNames from 'classnames';
import MatchMedia from './MatchMedia';
const ROOT_SELECTOR = '[data-js-custom-select]';

class CustomSelect {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.root = element;
		const parsedConfig = element.dataset.jsCustomSelect
			? JSON.parse(element.dataset.jsCustomSelect)
			: {};

		this.configAttr = {
			type: 'select',
			...parsedConfig,
		};

		this.config = {
			width: false,
			position: {
				my: 'left top',
				at: 'left bottom+10rem',
			},
			classes: {
				'ui-selectmenu-button': classNames('loveforever-select', {
					'loveforever-select--no-border': !this.configAttr.hasBorder,
				}),
				'ui-selectmenu-button-open': 'is-active',
				'ui-selectmenu-text': 'loveforever-select__value',
				'ui-selectmenu-icon': 'loveforever-select__icon',
				'ui-selectmenu-menu': `loveforever-select__menu loveforever-select__menu--${this.configAttr.type}`,
				'ui-selectmenu-open': 'is-active',
				'ui-selectmenu-disabled': 'is-disabled',
			},
			change: (event, ui) => {
				event.target.dispatchEvent(
					new Event('change', {
						bubbles: true,
					})
				);
			},
			...this.configAttr,
		};

		this.toogleSelectDeviceType();

		this.bindEvents();
	}

	init() {
		jQuery(this.root).selectmenu(this.config);
	}

	toogleSelectDeviceType(isMobile = MatchMedia.mobile.matches) {
		!isMobile ? this.init() : this.destroy();
	}

	bindEvents() {
		MatchMedia.mobile.addEventListener('change', (event) => {
			this.toogleSelectDeviceType(event.matches);
		});
	}

	destroy() {
		if (!jQuery(this.root).selectmenu('instance')) {
			return;
		}

		jQuery(this.root).selectmenu('destroy');
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
