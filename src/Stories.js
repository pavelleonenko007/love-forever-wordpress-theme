export default class StoriesCollection {
	selectors = {
		story: '[data-js-story]',
	}

	constructor() {
		this.stories = Array.from(document.querySelectorAll(this.selectors.story));
		this.dialog = document.getElementById('storiesDialog');

		this.currentStoryIndex = 0;

		this.bindEvents();
	}

	prevStory() {

	}

	nextStory() {

	}

	/**
	 * @param {number} index 
	 */
	open(index) {
		
	}

	close() {

	}

	bindEvents() {

	}

	destroy() {

	}
}