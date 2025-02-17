import { promiseWrapper, wait } from './utils';

const ROOT_SELECTOR = '[data-js-delete-fitting-button]';

export default class DeleteFittingButton {
	selectors = {
		root: ROOT_SELECTOR,
	};

	stateSelectors = {
		isLoading: 'is-loading',
	};

	constructor() {
		this.bindEvents();
	}

	/**
	 *
	 * @param {HTMLElement} button
	 */
	async deleteFitting(button) {
		try {
			const action = 'delete_fitting';
			const fittingId = button.dataset.jsDeleteFittingButton;
			const nonce = button.dataset.nonce;

			if (!fittingId) {
				return {
					success: false,
					message: 'Fitting ID не передан',
				};
			}

			if (!nonce) {
				return {
					success: false,
					message: 'Nonce не передан',
				};
			}

			const formData = new FormData();

			formData.append('action', action);
			formData.append('delete_fitting_nonce', nonce);
			formData.append('fitting_id', fittingId);

			const response = await fetch(LOVE_FOREVER.AJAX_URL, {
				method: 'POST',
				body: formData,
			});

			const body = await response.json();

			if (!body.success) {
				throw new Error(body.data.message);
			}

			return body.data;
		} catch (error) {
			throw error;
		}
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick = async (event) => {
		const { target } = event;

		if (!target.closest(this.selectors.root)) {
			return;
		}

		const deleteButton = target.closest(this.selectors.root);

		event.preventDefault();

		if (!confirm('Вы уверены, что хотите удалить примерку?')) {
			return;
		}

		deleteButton.classList.add(this.stateSelectors.isLoading);
		deleteButton.textContent = 'Удаление...';

		await wait(2_000);

		const { data, error } = await promiseWrapper(
			this.deleteFitting(deleteButton)
		);

		console.log({ data, error });

		if (error) {
			console.error(error);
			deleteButton.textContent = 'Удалить';
			deleteButton.classList.remove(this.stateSelectors.isLoading);
			return;
		}

		deleteButton.closest('.fitting-table__row').remove();
	};

	bindEvents() {
		document.addEventListener('click', this.onClick);
	}
}
