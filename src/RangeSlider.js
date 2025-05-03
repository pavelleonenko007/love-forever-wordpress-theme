import { formatPrice } from './utils';

const ROOT_SELECTOR = '[data-js-range-slider]';

class RangeSlider {
	selectors = {
		root: ROOT_SELECTOR,
		customRangeSliderComponent: '[data-js-range-slider-custom-component]',
		minControlElement: '[data-js-range-slider-control-min]',
		maxControlElement: '[data-js-range-slider-control-max]',
		minValueElement: '[data-js-range-slider-value-min]',
		maxValueElement: '[data-js-range-slider-value-max]',
	};

	constructor(element) {
		this.root = element;
		this.minControlElement = this.root.querySelector(
			this.selectors.minControlElement
		);
		this.maxControlElement = this.root.querySelector(
			this.selectors.maxControlElement
		);
		this.minValueElement = this.root.querySelector(
			this.selectors.minValueElement
		);
		this.maxValueElement = this.root.querySelector(
			this.selectors.maxValueElement
		);
		this.min = parseInt(this.minControlElement.getAttribute('min'));
		this.max = parseInt(this.maxControlElement.getAttribute('max'));
		this.minValue = parseInt(this.minControlElement.value);
		this.maxValue = parseInt(this.maxControlElement.value);

		this.customRangeSliderComponent = $(
			this.selectors.customRangeSliderComponent
		).slider({
			classes: {
				'ui-slider': 'lf-range-slider',
				'ui-slider-range': 'lf-range-slider__range',
				'ui-slider-handle': 'lf-range-slider__dot',
			},
			range: true,
			min: this.min,
			max: this.max,
			values: [this.minValue, this.maxValue],
			slide: (event, ui) => {
				if (this.minValue !== ui.values[0]) {
					this.minValue = ui.values[0];
					this.minControlElement.value = this.minValue;
					this.minControlElement.dispatchEvent(
						new Event('change', {
							bubbles: true,
						})
					);
				}

				if (this.maxValue !== ui.values[1]) {
					this.maxValue = ui.values[1];
					this.maxControlElement.value = this.maxValue;
					this.maxControlElement.dispatchEvent(
						new Event('change', {
							bubbles: true,
						})
					);
				}
				this.minValueElement.textContent = formatPrice(this.minValue);
				this.maxValueElement.textContent = formatPrice(this.maxValue);
			},
		});
	}

	destroy() {
		this.customRangeSliderComponent.slider('destroy');
	}
}

export default class RangeSliderCollection {
	/**
	 * @type {Map<string, RangeSlider>}
	 */
	static sliders = new Map();

	static getRangeSliderById(id) {
		return this.sliders.get(id);
	}

	static destroyAll() {
		this.sliders.forEach((slider, id) => {
			slider.destroy();
			setTimeout(() => this.sliders.delete(id));
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const rangeSlider = new RangeSlider(element);
			this.sliders.set(element.id, rangeSlider);
		});
	}
}
