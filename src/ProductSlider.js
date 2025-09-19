import Splide from '@splidejs/splide';

const ROOT_SELECTOR = '[data-js-product-slider]';

class ProductSlider {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.root = element;
		this.splideInstance = new Splide(this.root);

		this.splideInstance.mount();
	}

	destroy() {
		this.splideInstance.destroy();
	}
}

export default class ProductSliderCollection {
	static productSliders = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const productSlider = new ProductSlider(element);
			this.productSliders.set(element.id, productSlider);
		});
	}

	static destroyAll() {
		this.productSliders.forEach((slider, id) => {
			slider.destroy();
			setTimeout(() => this.productSliders.delete(id));
		});
	}
}
