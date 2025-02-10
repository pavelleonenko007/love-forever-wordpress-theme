const ROOT_SELECTOR = '[data-js-favorites-button]';

class FavoritesButtonWithCounter {
	selectors = {
		root: ROOT_SELECTOR,
		counter: '[data-js-favorites-button-counter]',
	};

	stateSelectors = {
		isActive: 'is-active',
	};

	/**
	 *
	 * @param {HTMLElement} element
	 */
	constructor(element) {
		this.favoritesButton = element;
		this.counter =
			this.favoritesButton.querySelector(this.selectors.counter) ??
			this.favoritesButton;

		this.onUpdated = this.onUpdated.bind(this);

		this.bindEvents();
	}

	/**
	 *
	 * @param {number} num
	 */
	_normalizeCount(num) {
		if (num < 0) {
			return 0;
		}

		return num;
	}

	/**
	 *
	 * @param {CustomEvent} event
	 */
	onUpdated(event) {
		const count = this._normalizeCount(event.detail.countFavorites);

		this.update(count);
	}

	/**
	 *
	 * @param {number} count
	 */
	update(count) {
		this.counter.textContent = count.toString();

		this.favoritesButton.classList.toggle(
			this.stateSelectors.isActive,
			count > 0
		);
	}

	bindEvents() {
		document.addEventListener('favoritesUpdated', this.onUpdated);
	}

	destroy() {
		document.removeEventListener('favoritesUpdated', this.onUpdated);
		this.favoritesButton = null;
		this.counter = null;
	}
}

export default class FavoritesButtonWithCounterCollection {
	/**
	 * @type {Map<HTMLElement, FavoritesButtonWithCounter>}
	 */
	static favoriteButtonsWithCounters = new Map();
	static init() {
		const buttonsWithCounter = document.querySelectorAll(ROOT_SELECTOR);

		buttonsWithCounter.forEach((button) => {
			const favoriteButtonsWithCounterInstance = new FavoritesButtonWithCounter(
				button
			);
			FavoritesButtonWithCounterCollection.favoriteButtonsWithCounters.set(
				button,
				favoriteButtonsWithCounterInstance
			);
		});
	}

	static destroyAll() {
		FavoritesButtonWithCounterCollection.favoriteButtonsWithCounters.forEach(
			(buttonComponent, element) => {
				buttonComponent.destroy();
				FavoritesButtonWithCounterCollection.favoriteButtonsWithCounters.delete(
					element
				);
			}
		);
	}
}
