import MatchMedia from './MatchMedia';

const ROOT_SELECTOR = '[data-js-datepicker]';

class CustomDatepicker {
	selectors = {
		root: ROOT_SELECTOR,
		originalControl: '[data-js-datepicker-original-control]',
		customControl: '[data-js-datepicker-custom-control]',
	};

	/**
	 * @param {HTMLElement} element
	 */
	constructor(element) {
		this.root = element;
		this.originalControl = this.root.querySelector(
			this.selectors.originalControl
		);
		this.customControl = this.root.querySelector(this.selectors.customControl);

		this.toggleControls();

		const datepickerConfig = this.customControl.dataset.jsDatepickerConfig
			? JSON.parse(this.customControl.dataset.jsDatepickerConfig)
			: {};

		$(this.customControl).datepicker({
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
			altField: this.originalControl,
			altFormat: 'yy-mm-dd',
			onSelect: (dateString, instance) => {
				this.originalControl.dispatchEvent(
					new Event('change', {
						bubbles: true,
					})
				);
			},
			...datepickerConfig,
		});

		this.bindEvents();
	}

	toggleControls(isMobile = MatchMedia.mobile.matches) {
		this.originalControl.hidden = !isMobile;
		this.customControl.hidden = isMobile;

		if (isMobile) {
			$(this.customControl).datepicker('hide');
		}
	}

	onMatchMediaChange = (event) => {
		this.toggleControls(event.matches);
	};

	bindEvents() {
		MatchMedia.mobile.addEventListener('change', this.onMatchMediaChange);
	}

	destroy() {
		MatchMedia.mobile.removeEventListener('change', this.onMatchMediaChange);
		$(this.customControl).datepicker('destroy');
	}
}

export default class CustomDatepickerCollection {
	static customDatepickers = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const CustomDatepickerInstance = new CustomDatepicker(element);
			CustomDatepickerCollection.customDatepickers.set(
				element,
				CustomDatepickerInstance
			);
		});
	}

	static destroyAll() {
		CustomDatepickerCollection.customDatepickers.forEach((instance) => {
			instance.destroy();
		});

		CustomDatepickerCollection.customDatepickers.clear();
	}
}
