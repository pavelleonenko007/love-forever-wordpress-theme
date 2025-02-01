import Barba from 'barba.js';

export default class AddToFavoriteButtonCollection {
	selectors = {
		root: '[data-js-add-to-favorite-button]',
		text: '[data-js-add-to-favorite-button-text]',
	};

	stateSelectors = {
		isActive: 'is-active',
		isLoading: 'is-loading',
	};

	stateTexts = {
		add: 'Добавить в избранное',
		remove: 'Удалить из избранного',
	};

	constructor() {
		this.onClick = this.onClick.bind(this);
		this.bindEvents();
	}

	/**
	 *
	 * @param {HTMLButtonElement} button
	 */
	async onAddToFavoritesButtonClick(button) {
		const productId = button.dataset.jsAddToFavoriteButton;

		if (!productId) {
			return;
		}

		button.disabled = true;
		button.classList.add(this.stateSelectors.isLoading);

		try {
			const formData = new FormData();

			formData.append('action', 'add_product_to_favorites');
			formData.append('nonce', LOVE_FOREVER.NONCE);
			formData.append('product-id', productId);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			if (!body.success) {
				console.error(body.data.debug);
				throw new Error(body.data.message);
			}

			Barba.BaseCache.reset();

			const textElement = button.querySelector(this.selectors.text);

			if (button.classList.contains(this.stateSelectors.isActive)) {
				button.classList.remove(this.stateSelectors.isActive);

				if (textElement) {
					textElement.textContent = this.stateTexts.add;
				}
			} else {
				button.classList.add(this.stateSelectors.isActive);

				if (textElement) {
					textElement.textContent = this.stateTexts.remove;
				}
			}

			button.dispatchEvent(
				new CustomEvent('favoritesUpdated', {
					bubbles: true,
					detail: {
						countFavorites: body.data.countFavorites,
					},
				})
			);
		} catch (error) {
			console.error(error.message);
			window.alert(error.message);
		} finally {
			button.disabled = false;
			button.classList.remove(this.stateSelectors.isLoading);
		}
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick(event) {
		const addToFavoritesButton = event.target.closest(this.selectors.root);

		if (addToFavoritesButton) {
			event.preventDefault();

			this.onAddToFavoritesButtonClick(addToFavoritesButton);
		}
	}

	bindEvents() {
		document.addEventListener('click', this.onClick);
	}
}
