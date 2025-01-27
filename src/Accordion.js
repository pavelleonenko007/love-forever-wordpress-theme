const ROOT_SELECTOR = '.accordion';

class Accordion {
	constructor(element) {
		this.accordion = element;
		this.triggers = [
			...this.accordion.querySelectorAll('[data-js-accordion-trigger]'),
		];

		this.init();
	}

	init() {
		this.triggers.forEach((trigger) => {
			// Set unique IDs for ARIA attributes
			const panel = trigger.parentElement.nextElementSibling;
			const panelId = panel.id;
			trigger.setAttribute('aria-controls', panelId);
			trigger.id = `${panelId}-trigger`;
			panel.setAttribute('aria-labelledby', trigger.id);

			// Add event listeners
			trigger.addEventListener('click', () => this.togglePanel(trigger));
			// trigger.addEventListener('keydown', (e) => this.handleKeydown(e));
		});
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
}

export class AccordionCollection {
	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((accordion) => {
			new Accordion(accordion);
		});
	}
}
// Initialize all accordions on the page
