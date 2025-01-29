import BaseComponent from './BaseComponent';
import { getCookie, setCookie } from './utils';

const ROOT_SELECTOR = '[data-js-add-to-favorite-button]';

class AddToFavoriteButton extends BaseComponent {
	selectors = {
		root: ROOT_SELECTOR,
		textElement: '[data-js-add-to-favorite-button-text]',
	};

	stateValues = {
		inactive: 'Добавить в избранное',
		active: 'Удалить из избранного',
	};

	stateSelector = {
		isActive: 'is-active',
		isLoading: 'is-loading',
	};

	stateAttributes = {
		ariaLabel: 'aria-label',
	};

	constructor(element) {
		super();
		this.rootElement = element;
		this.textElement = this.rootElement.querySelector(
			this.selectors.textElement
		);

		this.state = this._getProxyState({
			isLoading: false,
			status: this.rootElement.classList.contains(this.stateSelector.isActive)
				? 'active'
				: 'inactive',
		});

		this.addToFavorites = this.addToFavorites.bind(this);

		this.bindEvents();
	}

	addToFavorites() {
		this.state.isLoading = true;

		const productId = this.rootElement.dataset.jsAddToFavoriteButton;

		if (!productId) {
			this.state.isLoading = false;
			return;
		}

		const favoritesCookie = getCookie('favorites') ?? '';
		const favorites = favoritesCookie.split(',');

		if (favorites.includes(productId)) {
			favorites.splice(favorites.indexOf(productId), 1);
			this.state.status = 'inactive';
		} else {
			favorites.push(productId);
			this.state.status = 'active';
		}

		setCookie('favorites', favorites.join(','));

		this.state.isLoading = false;
	}

	updateUI() {
		this.rootElement.disabled = this.state.isLoading;
		this.rootElement.classList.toggle(
			this.stateSelector.isLoading,
			this.state.isLoading
		);
		this.rootElement.classList.toggle(
			this.stateSelector.isActive,
			this.state.status === 'active'
		);

		if (this.textElement) {
			this.textElement.textContent = this.stateValues[this.state.status];
		}
	}

	bindEvents() {
		this.rootElement.addEventListener('click', this.addToFavorites);
	}

	destroy() {
		this.rootElement.removeEventListener('click', this.addToFavorites);
	}
}

class AddToFavoriteButtonCollection {
	/**
	 * @type {Map<string, AddToFavoriteButton>}
	 */
	static addToFavoriteButtons = new Map();

	static destroyAll() {
		this.addToFavoriteButtons.forEach((addToFavoriteButton, id) => {
			addToFavoriteButton.destroy();
			this.addToFavoriteButtons.delete(id);
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			const addToFavoriteButtonInstance = new AddToFavoriteButton(element);
			this.addToFavoriteButtons.set(element.id, addToFavoriteButtonInstance);
		});
	}
}

export default AddToFavoriteButtonCollection;
