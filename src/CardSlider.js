const ROOT_SELECTOR = "[data-js-card-slider]";

class CardSlider {
  selectors = {
    root: ROOT_SELECTOR,
    slideItem: "[data-js-card-slider-slide-item]",
    navItem: "[data-js-card-slider-nav-item]",
  };

  stateSelectors = {
    isActive: "is-active",
  };

  /**
   *
   * @param {HTMLElement} element
   */
  constructor(element) {
    this.root = element;

    this.bindEvents();
  }

  /**
   *
   * @param {number} num
   */
  activateSlide(num) {
    this.root
      .querySelectorAll(this.selectors.slideItem)
      .forEach((slideItem, index) => {
        slideItem.classList.toggle(this.stateSelectors.isActive, index === num);
      });

    this.root
      .querySelectorAll(this.selectors.navItem)
      .forEach((navItem, index) => {
        navItem.classList.toggle(this.stateSelectors.isActive, index === num);
      });
  }

  /**
   *
   * @param {MouseEvent} event
   */
  onMouseOver = (event) => {
    const { target } = event;
    const navItem = target.closest(this.selectors.navItem);

    if (!navItem) {
      return;
    }

    const activeSlideIndex = parseInt(navItem.dataset.jsCardSliderNavItem);

    this.activateSlide(activeSlideIndex);
  };

  bindEvents() {
    this.root.addEventListener("mouseover", this.onMouseOver);
  }

  destroy() {
    this.root.removeEventListener("mouseover", this.onMouseOver);
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
