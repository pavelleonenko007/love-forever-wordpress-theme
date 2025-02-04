import { lockFocus } from './utils';

const ROOT_SELECTOR = '[data-js-dialog]';

class Dialog {
	selectors = {
		dialog: '[data-js-dialog]',
		dialogContent: '[data-js-dialog-content]',
		dialogOverlay: '[data-js-dialog-overlay]',
		closeButton: '[data-js-dialog-close-button]',
		openButton: '[data-js-dialog-open-button]',
	};

	stateSelectors = {
		isOpen: 'is-open',
		isLocked: 'is-locked',
	};

	constructor(dialogElement) {
		this.dialog = dialogElement;

		if (!this.dialog) {
			throw new Error('Dialog element not found');
		}

		this.openButtons = document.querySelectorAll(this.selectors.openButton);
		this.closeButton = this.dialog.querySelector(this.selectors.closeButton);

		this.closeCallback = null;

		this.onClick = this.onClick.bind(this);
		this.onKeyDown = this.onKeyDown.bind(this);

		this.bindEvents();
	}

	open() {
		if (this.dialog.classList.contains(this.stateSelectors.isOpen)) {
			return;
		}
		
		this.dialog.classList.add(this.stateSelectors.isOpen);
		document.documentElement.classList.add(this.stateSelectors.isLocked);

		this.closeCallback = lockFocus(this.dialog);

		this.dialog.dispatchEvent(
			new CustomEvent('dialogOpen', {
				bubbles: true,
				detail: {
					dialogId: this.dialog.id,
				},
			})
		);
	}

	close() {
		if (!this.dialog.classList.contains(this.stateSelectors.isOpen)) {
			return;
		}

		this.dialog.classList.remove(this.stateSelectors.isOpen);
		document.documentElement.classList.remove(this.stateSelectors.isLocked);

		if (this.closeCallback) {
			this.closeCallback();
			this.closeCallback = null;
		}

		this.dialog.dispatchEvent(
			new CustomEvent('dialogClose', {
				bubbles: true,
				detail: {
					dialogId: this.dialog.id,
				},
			})
		);
	}

	toggle() {
		this.dialog.classList.toggle(this.stateSelectors.isOpen);
		document.documentElement.classList.toggle(this.stateSelectors.isLocked);

		if (this.dialog.classList.contains(this.stateSelectors.isOpen)) {
			this.closeCallback = lockFocus(this.dialog);
		} else {
			if (this.closeCallback) {
				this.closeCallback();
				this.closeCallback = null;
			}
		}
	}

	onClick(event) {
		const openButton = event.target.closest(this.selectors.openButton);

		if (openButton) {
			event.preventDefault();

			if (openButton.dataset.jsDialogOpenButton === this.dialog.id) {
				this.open();
				return;
			}
		}

		if (
			event.target.matches(this.selectors.closeButton) ||
			!event.target.closest(this.selectors.dialogContent)
		) {
			this.close();
			return;
		}
	}

	onKeyDown(event) {
		if (event.key === 'Escape') {
			event.preventDefault();

			this.close();
		}
	}

	bindEvents() {
		document.body.addEventListener('click', this.onClick);
		this.dialog.addEventListener('keydown', this.onKeyDown);
	}

	destroy() {
		document.body.removeEventListener('click', this.onClick);
		this.dialog.removeEventListener('keydown', this.onKeyDown);
	}
}

class DialogCollection {
	/**
	 * @type {Map<string, Dialog>}
	 */
	static dialogs = new Map();

	static getDialogsById(id) {
		return this.dialogs.get(id);
	}

	static openDialogById(id) {
		const dialog = this.dialogs.get(id);

		if (dialog) {
			dialog.open();
		}
	}

	static closeDialogById(id) {
		const dialog = this.dialogs.get(id);

		if (dialog) {
			dialog.close();
		}
	}

	static closeAllDialogs() {
		this.dialogs.forEach((dialog) => {
			dialog.close();
		});

		return Promise.resolve();
	}

	static destroyAll() {
		this.dialogs.forEach((dialog, id) => {
			dialog.destroy();
			this.dialogs.delete(id);
		});
	}

	static init() {
		const dialogs = document.querySelectorAll(ROOT_SELECTOR);

		dialogs.forEach((dialogElement) => {
			const dialog = new Dialog(dialogElement);

			this.dialogs.set(dialogElement.id, dialog);
		});
	}
}

export default DialogCollection;
