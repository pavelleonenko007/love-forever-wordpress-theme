import BaseComponent from './BaseComponent';
import { copyTextToClipboard } from './utils';

const ROOT_SELECTOR = '[data-js-copy-button]';

class CopyToClipboardButton extends BaseComponent {
	selectors = {
		root: ROOT_SELECTOR,
		buttonText: '[data-js-copy-button-text]',
	};

	/**
	 * @param {HTMLButtonElement} element
	 */
	constructor(element) {
		super();
		this.button = element;
		this.buttonTextElement =
			this.button.querySelector(this.selectors.buttonText) ?? this.button;

		this.textToCopy = this.button.dataset.jsCopyButton;
		this.buttonText = this.buttonTextElement.textContent;

		this.state = this._getProxyState({
			isCopied: false,
			hasError: false,
		});

		this.onClick = this.onClick.bind(this);

		this.bindEvents();
	}

	updateUI() {
		this.button.disabled = this.state.isCopied;

		if (!this.state.hasError) {
			this.buttonTextElement.textContent = this.state.isCopied
				? 'Скопировано'
				: this.buttonText;
		} else {
			this.buttonTextElement.textContent = 'Ошибка при копировании';
		}
	}

	onClick(event) {
		if (!event.target.closest(this.selectors.root)) {
			return;
		}

		event.preventDefault();

		copyTextToClipboard(this.textToCopy)
			.then(() => {
				this.state.isCopied = true;
				setTimeout(() => {
					this.state.isCopied = false;
				}, 3_000);
			})
			.catch(() => {
				this.state.hasError = true;
				setTimeout(() => {
					this.state.hasError = false;
				}, 3_000);
			});
	}

	bindEvents() {
		this.button.addEventListener('click', this.onClick);
	}

	destroy() {
		this.button.addEventListener('remove', this.onClick);
		this.state = null;
	}
}

export default class CopyToClipboardButtonCollection {
	/**
	 * @type {Map<HTMLButtonElement, CopyToClipboardButton>}
	 */
	static copyButtons = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((button) => {
			const copyButtonInstance = new CopyToClipboardButton(button);
			this.copyButtons.set(button, copyButtonInstance);
		});
	}

	static destroyAll() {
		this.copyButtons.forEach((copyButtonInstance) => {
			copyButtonInstance.destroy();
		});
		this.copyButtons.clear();
	}
}
