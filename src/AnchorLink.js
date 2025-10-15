import { scrollToElement } from './utils';

const ROOT_SELECTOR = '[data-js-anchor-link]';

class AnchorLink {
	selectors = {
		root: ROOT_SELECTOR,
	};

	constructor(element) {
		this.root = element;
		this.config = {
			easing: (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2),
			...this.parseConfig(),
		};

		this.bindEvents();
	}

	parseConfig() {
		try {
			return JSON.parse(this.root.dataset.jsAnchorLink);
		} catch (error) {
			return {};
		}
	}

	onClick = (event) => {
		event.preventDefault();
		event.stopPropagation();

		const url = new URL(this.root.href);
		const targetElement = document.querySelector(url.hash);
		if (!targetElement) {
			return;
		}

		scrollToElement(targetElement, this.config).then(() => {
			window.history.pushState(null, '', this.root.href);
		});
	};

	bindEvents() {
		this.root.addEventListener('click', this.onClick);
	}
}

export default class AnchorLinkCollection {
	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((element) => {
			new AnchorLink(element);
		});
	}
}
