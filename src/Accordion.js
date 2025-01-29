const ROOT_SELECTOR = '.accordion';

class Accordion {
	selectors = {
		root: ROOT_SELECTOR,
		trigger: '[data-js-accordion-trigger]',
	};

	constructor(element) {
		this.accordion = element;
		this.triggers = this.accordion.querySelectorAll(this.selectors.trigger);

		this.onClick = this.onClick.bind(this);

		this.bindEvents();
	}

	togglePanel(trigger) {
		const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
		const panel = document.getElementById(
			trigger.getAttribute('aria-controls')
		);

		trigger.setAttribute('aria-expanded', !isExpanded);
		panel.hidden = isExpanded;
	}

	handleKeydown(event) {
		const trigger = event.target;
		const triggerIndex = this.triggers.indexOf(trigger);

		switch (event.key) {
			case 'ArrowDown':
				event.preventDefault();
				this.triggers[triggerIndex + 1]?.focus();
				break;
			case 'ArrowUp':
				event.preventDefault();
				this.triggers[triggerIndex - 1]?.focus();
				break;
			case 'Home':
				event.preventDefault();
				this.triggers[0].focus();
				break;
			case 'End':
				event.preventDefault();
				this.triggers[this.triggers.length - 1].focus();
				break;
		}
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick(event) {
		event.preventDefault();

		const trigger = event.target.closest('[data-js-accordion-trigger]');
		if (!trigger) {
			return;
		}

		this.togglePanel(trigger);
	}

	bindEvents() {
		[...this.triggers].forEach((trigger) => {
			trigger.addEventListener('click', this.onClick);
		});
	}

	destroy() {
		[...this.triggers].forEach((trigger) => {
			trigger.removeEventListener('click', this.onClick);
		});
	}
}

export class AccordionCollection {
	/**
	 * @type {Map<string, Accordion>}
	 */
	static accordions = new Map();

	static destroyAll() {
		this.accordions.forEach((accordion, id) => {
			accordion.destroy();
			this.accordions.delete(id);
		});
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((accordion) => {
			const accordionInstance = new Accordion(accordion);
			this.accordions.set(accordion.id, accordionInstance);
		});
	}
}
