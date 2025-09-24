import Splide from '@splidejs/splide';

const ROOT_SELECTOR = '[data-js-review-splide]';

class ReviewImageSlider {
	selectors = {
		root: ROOT_SELECTOR,
		list: '.splide__list',
		page: '[data-splide-page]',
	};

	constructor(element) {
		this.element = element;
		this.config = {
			perPage: 1,
			perMove: 1,
			type: 'loop',
			pagination: true,
			arrows: true,
			autoplay: false,
			drag: false,
			speed: 500,
			classes: {
				arrows: 'lf-review-splide__arrows splide__arrows',
				arrow: 'lf-review-splide__arrow splide__arrow',
				pagination: 'lf-review-splide__pagination splide__pagination',
				page: 'lf-review-splide__page splide__pagination__page',
			},
			// arrowPath: 'M5.25 4.286.75 0 0 .714 4.5 5 0 9.286.75 10 6 5l-.75-.714Z',
		};
		this.splideInstance = new Splide(this.element, this.config);
		this.splideInstance.mount({
			SlideNumberExtension: this.SlideNumberExtension,
		});
	}

	SlideNumberExtension(Splide, Components) {
		const { track } = Components.Elements;

		let elm;

		function mount() {
			elm = document.createElement('div');
			elm.classList.add('lf-review-splide__slide-number');

			track.parentElement.insertBefore(elm, track.nextSibling);

			update();
			Splide.on('move', update);
		}

		function update() {
			elm.textContent = `${Splide.index + 1} / ${Splide.length}`;
		}

		return {
			mount,
		};
	}
}

export default class ReviewImageSliderCollection {
	static reviewImageSliders = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const reviewImageSliderInstance = new ReviewImageSlider(element);
			ReviewImageSliderCollection.reviewImageSliders.set(
				element,
				reviewImageSliderInstance
			);
		});
	}
}
