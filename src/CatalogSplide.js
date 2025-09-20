import Splide from '@splidejs/splide';


const ROOT_SELECTOR = '[data-js-catalog-splide]';

class CatalogSplide {
	constructor(element) {
		this.element = element;
		this.splideInstance = new Splide(this.element);
		this.splideInstance.mount();
	}
}

export default class CatalogSplideCollection {
	static catalogSlides = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const catalogSplideInstance = new CatalogSplide(element);
			CatalogSplideCollection.catalogSlides.set(element, catalogSplideInstance);
		});
	}
};