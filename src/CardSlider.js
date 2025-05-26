import Splide from '@splidejs/splide';

// const ROOT_SELECTOR = '[data-js-card-slider]';
const ROOT_SELECTOR = '[data-js-card-splide]';

// class CardSlider {
// 	selectors = {
// 		root: ROOT_SELECTOR,
// 		slideItem: '[data-js-card-slider-slide-item]',
// 		navItem: '[data-js-card-slider-nav-item]',
// 	};

// 	stateSelectors = {
// 		isActive: 'is-active',
// 	};

// 	/**
// 	 *
// 	 * @param {HTMLElement} element
// 	 */
// 	constructor(element) {
// 		this.root = element;

// 		this.bindEvents();
// 	}

// 	/**
// 	 *
// 	 * @param {number} num
// 	 */
// 	activateSlide(num) {
// 		this.root
// 			.querySelectorAll(this.selectors.slideItem)
// 			.forEach((slideItem, index) => {
// 				slideItem.classList.toggle(this.stateSelectors.isActive, index === num);
// 			});

// 		this.root
// 			.querySelectorAll(this.selectors.navItem)
// 			.forEach((navItem, index) => {
// 				navItem.classList.toggle(this.stateSelectors.isActive, index === num);
// 			});
// 	}

// 	/**
// 	 *
// 	 * @param {MouseEvent} event
// 	 */
// 	onMouseOver = (event) => {
// 		const { target } = event;
// 		const navItem = target.closest(this.selectors.navItem);

// 		if (!navItem) {
// 			return;
// 		}

// 		const activeSlideIndex = parseInt(navItem.dataset.jsCardSliderNavItem);

// 		this.activateSlide(activeSlideIndex);
// 	};

// 	onMouseLeave = (event) => {
// 		this.activateSlide(0);
// 	};

// 	bindEvents() {
// 		this.root.addEventListener('mouseover', this.onMouseOver);
// 		this.root.addEventListener('mouseleave', this.onMouseLeave);
// 	}

// 	destroy() {
// 		this.root.removeEventListener('mouseover', this.onMouseOver);
// 		this.root.removeEventListener('mouseleave', this.onMouseLeave);
// 	}
// }

class CardSlider {
	selectors = {
		root: ROOT_SELECTOR,
		list: '.splide__list',
		page: '[data-splide-page]',
	};

	/**
	 *
	 * @param {HTMLElement} element
	 */
	constructor(element) {
		this.root = element;
		this.splideInstance = new Splide(this.root);

		this.bindEvents();

		this.splideInstance.mount();

		const originalTranslate = this.splideInstance.Components.Move.translate;

		this.splideInstance.Components.Move.translate = (position, preventLoop) => {
			if (!this.splideInstance.is('fade')) {
				const destination = position;
				const transform = `translate${this.splideInstance.Components.Direction.resolve(
					'X'
				)}(${destination}px) translateZ(0)`;
				this.splideInstance.Components.Elements.list.style.transform =
					transform;

				if (position !== destination) {
					this.splideInstance.emit('shifted');
				}
			}
		};
	}

	onPaginationMounted = (data) => {
		data.items.forEach(function (item) {
			item.button.dataset.splidePage = item.page;
		});
	};

	onDrag = () => {
		/**
		 * @type {HTMLElement}
		 */
		const list = this.root.querySelector(this.selectors.list);
		list.style.willChange = 'transform';
	};

	onMoved = () => {
		/**
		 * @type {HTMLElement}
		 */
		const list = this.root.querySelector(this.selectors.list);
		list.style.willChange = null;
	};

	onMouseOver = (event) => {
		const paginationButton = event.target.closest(this.selectors.page);

		if (!paginationButton) return;

		const page = parseInt(paginationButton.dataset.splidePage);

		this.splideInstance.go(page);
	};

	onMouseLeave = (event) => {
		this.splideInstance.go(0);
	};

	bindEvents() {
		this.splideInstance.on('pagination:mounted', this.onPaginationMounted);
		this.splideInstance.on('drag', this.onDrag);
		this.splideInstance.on('moved', this.onMoved);

		this.root.addEventListener('mouseover', this.onMouseOver);
		this.root.addEventListener('mouseleave', this.onMouseLeave);
	}

	destroy() {
		this.splideInstance.destroy();
		this.root.removeEventListener('mouseover', this.onMouseOver);
		this.root.removeEventListener('mouseleave', this.onMouseLeave);
	}
}

export default class CardSliderCollection {
	/**
	 * @type {Map<HTMLElement, CardSlider>}
	 */
	static cardSliders = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const cardSliderInstance = new CardSlider(element);

			CardSliderCollection.cardSliders.set(element, cardSliderInstance);
		});
	}

	static destroyAll() {
		CardSliderCollection.cardSliders.forEach((cardSlider) => {
			cardSlider.destroy();
		});
		CardSliderCollection.cardSliders.clear();
	}
}
