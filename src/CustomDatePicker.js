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

		$.datepicker.setDefaults($.datepicker.regional['ru']);

		$(this.customControl).datepicker({
			classes: {
				'ui-datepicker': 'lf-datepicker',
			},
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'd MM (D)',
			monthNames: [
				'Января',
				'Февраля',
				'Марта',
				'Апреля',
				'Мая',
				'Июня',
				'Июля',
				'Августа',
				'Сентября',
				'Октября',
				'Ноября',
				'Декабря',
			],
			altField: this.originalControl,
			altFormat: 'yy-mm-dd',
			onSelect: (dateString, instance) => {
				console.log({ dateString });

				this.originalControl.dispatchEvent(
					new Event('change', {
						bubbles: true,
					})
				);
			},
			beforeShow: (input, inst) => {
				var calendar = inst.dpDiv;

				// Dirty hack, but we can't do anything without it (for now, in jQuery UI 1.8.20)
				setTimeout(() => {
					$(this.root).append(calendar);

					calendar.position({
						my: 'left top',
						at: 'left bottom+10rem',
						collision: 'none',
						of: input,
					});
				}, 0);
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
			// $(this.customControl).datepicker('disable');
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
