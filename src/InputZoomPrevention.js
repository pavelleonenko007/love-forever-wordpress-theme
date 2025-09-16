import { viewportManager } from './ViewportManager';

const ROOT_SELECTOR = '[data-js-input-zoom-prevention]';

class InputZoomPrevention {
	constructor(root) {
		this.inputs = root.querySelectorAll('input:not([type="submit"]):not([type="reset"]):not([type="hidden"]):not([type="radio"]):not([type="checkbox"]), textarea');
		
		this.init();
	}

	init() {
		this.bindEvents();
	}

	bindEvents() {
		this.inputs.forEach(input => {
			input.addEventListener('focus', this.handleFocus.bind(this), {
				capture: true,
			});
			input.addEventListener('blur', this.handleBlur.bind(this), {
				capture: true,
			});
		});
	}

	handleFocus(event) {
		// Блокируем viewport только на мобильных устройствах
		if (window.innerWidth <= 768) {
			viewportManager.lockViewport();
		}
	}

	handleBlur(event) {
		// Разблокируем viewport с небольшой задержкой
		// чтобы клавиатура успела скрыться
		setTimeout(() => {
			viewportManager.unlockViewport();
		}, 300);
	}

	destroy() {
		this.inputs.forEach(input => {
			input.removeEventListener('focus', this.handleFocus.bind(this));
			input.removeEventListener('blur', this.handleBlur.bind(this));
		});
	}
}

export default InputZoomPrevention;