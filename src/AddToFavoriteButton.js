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
		const productId = parseInt(button.dataset.jsAddToFavoriteButton);

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

			const textElement = button.querySelector(this.selectors.text);
			const isActive = body.data.favorites.includes(productId);

			button.classList.toggle(this.stateSelectors.isActive, isActive);

			if (textElement) {
				textElement.textContent = isActive
					? this.stateTexts.remove
					: this.stateTexts.add;
			}

			button.dispatchEvent(
				new CustomEvent('favoritesUpdated', {
					bubbles: true,
					detail: {
						productId: productId,
						countFavorites: body.data.countFavorites,
						isActive,
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

	// По хорошему сделать каждую кнопку отдельным объектом и использовать его методы, а не искать все кнопки в DOM
	onFavoritesUpdated = (event) => {
		const { productId, isActive } = event.detail;

		document.querySelectorAll(this.selectors.root).forEach((button) => {
			if (button.dataset.jsAddToFavoriteButton === productId.toString()) {
				button.classList.toggle(this.stateSelectors.isActive, isActive);

				const textElement = button.querySelector(this.selectors.text);

				if (textElement) {
					textElement.textContent = isActive ? this.stateTexts.remove : this.stateTexts.add;
				}
			}
		});
	}

	bindEvents() {
		document.addEventListener('click', this.onClick);
		document.addEventListener('favoritesUpdated', this.onFavoritesUpdated);
	}
}
