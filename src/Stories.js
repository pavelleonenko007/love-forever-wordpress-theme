import '@splidejs/splide/css/core';

import Splide from '@splidejs/splide';
import DialogCollection from './Dialog';

const ROOT_SELECTOR = '[data-js-stories]';
export default class Stories {
	selectors = {
		rootSlider: ROOT_SELECTOR,
		innerSlider: '[data-js-story]',
		storyCta: '[data-js-story-cta]',
	};

	constructor(element, options = { startSlide: 0 }) {
		this.element = element;
		this.stories = new Map();
		this.activeStoryIndex = options.startSlide;

		this.pressStartTime = 0;
		this.holdTimeout = null;

		this.holdDelay = 300;

		this.root = new Splide(this.element, {
			width: '465rem',
			perMove: 1,
			perPage: 1,
			pagination: false,
			arrows: false,
			speed: 0,
			drag: false,
			breakpoints: {
				479: {
					width: '100vw',
					padding: 0,
					gap: 0,
				},
			},
		});

		this.root.mount();
		this.root.on('moved', this.onMovedRootSlider);
		this.root.on('move', this.onMoveRootSlider);
		this.root.on('move', this.updateRootSlidesScale);
		this.root.on('click', this.onRootClick);

		this.element
			.querySelectorAll(this.selectors.innerSlider)
			.forEach((element, index) => {
				const instance = new Splide(element, {
					perPage: 1,
					perMove: 1,
					type: 'fade',
					autoWidth: true,
					pagination: true,
					width: '465rem',
					arrows: false,
					autoplay: true,
					pauseOnHover: false,
					drag: false,
				});

				this.stories.set(index, instance);

				instance.on('mounted', () => {
					instance.Components.Slides.get().forEach((splideSlide) => {
						const { slide } = splideSlide;

						slide.dataset.splideInterval = this.getSlideInterval(slide);
					});

					instance.Components.Autoplay.pause();
				});

				instance.mount();

				instance.on('move', (newIndex, prevIndex) => {
					const activeStory = this.stories.get(this.activeStoryIndex);
					const slides = activeStory.Components.Slides.get();

					for (let i = 0; i < slides.length; i++) {
						const splideSlide = slides[i];

						const slide = splideSlide.slide;
						const isActiveSlide = splideSlide.index === newIndex;
						const video = this.hasVideo(slide);

						if (!video) {
							continue;
						}

						if (isActiveSlide) {
							video.play();
						} else {
							video.pause();
							video.currentTime = 0;
						}
					}
				});

				instance.on('moved', () => {
					const activeStory = this.stories.get(this.activeStoryIndex);
					activeStory.Components.Autoplay.play();
				});
			});

		this.openStory(this.activeStoryIndex);

		this.bindEvents();
	}

	bindEvents() {
		window.addEventListener('dialogClose', this.onDialogClose);
	}

	onDialogClose = () => {
		this.destroy();
	};

	destroy() {
		window.removeEventListener('dialogClose', this.onDialogClose);
		this.root.destroy();
		this.stories.forEach((storySplide) => {
			storySplide.root.removeEventListener('pointerdown', this.onPointerDown);
			storySplide.destroy();
		});
		this.stories.clear();

		setTimeout(() => {
			this.root = null;
			this.element = null;
		});
	}

	onMoveRootSlider = () => {
		this.stories.forEach((splide) => {
			splide.Components.Autoplay.pause();
			splide.Components.Slides.get().forEach((splideSlide) => {
				const video = this.hasVideo(splideSlide.slide);

				if (video) {
					video.pause();
					video.currentTime = 0;
				}
			});
		});
	};

	onMovedRootSlider = (newIndex, prevIndex, destIndex) => {
		this.stories.get(newIndex).Components.Autoplay.play();
		this.clearHold();
	};

	setupProgressbars = (newIndex) => {
		const activeStory = this.stories.get(this.activeStoryIndex);
		const { items } = activeStory.Components.Pagination;

		items.forEach((item, index) => {
			if (index > newIndex) {
				item.button.style.setProperty('--progress', 0);
			}

			if (index < newIndex) {
				item.button.style.setProperty('--progress', 1);
			}
		});
	};

	updateRootSlidesScale = (newIndex) => {
		const slides = this.root.Components.Slides.get();

		slides.forEach((slide, i) => {
			if (i === newIndex) {
				slide.slide.classList.add('is-active');
			} else {
				slide.slide.classList.remove('is-active');
			}
		});
	};

	onRootClick = (slide) => {
		setTimeout(() => {
			if (this.root.state.is(Splide.STATES.MOVING)) {
				return;
			}

			if (this.activeStoryIndex !== slide.index) {
				this.openStory(slide.index);
			}
		}, 0);
	};

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onPointerDown = (event) => {
		console.log(event.target);

		if (
			event.target.closest(this.selectors.storyCta) ||
			event.target.closest('.splide__pagination__page')
		) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();

		this.pressStartTime = Date.now();

		const activeStory = this.stories.get(this.activeStoryIndex);

		const { Autoplay } = activeStory.Components;
		const video = this.hasVideo(
			activeStory.Components.Slides.getAt(activeStory.index).slide
		);

		this.holdTimeout = setTimeout(() => {
			Autoplay.pause();

			if (video) {
				video.pause();
				video.currentTime = 0;
			}
		}, this.holdDelay);

		const onPointerUp = (event) => {
			this.clearHold();

			const pressDuration = Date.now() - this.pressStartTime;
			const containerWidth = activeStory.root.offsetWidth;

			if (pressDuration < this.holdDelay) {
				if (event.offsetX < containerWidth / 2) {
					if (activeStory.index === 0) {
						this.openStory(this.activeStoryIndex - 1);
					} else {
						activeStory.go('<');
					}
				} else {
					if (activeStory.index === activeStory.length - 1) {
						this.openStory(this.activeStoryIndex + 1);
					} else {
						activeStory.go('>');
					}
				}
			} else {
				Autoplay.play();
				video?.play();
			}
		};

		activeStory.root.addEventListener('pointerup', onPointerUp, {
			once: true,
		});
	};

	clearHold = () => {
		clearTimeout(this.holdTimeout);
		this.holdTimeout = null;
	};

	openStory(index = 0) {
		if (index >= this.root.length) {
			DialogCollection.getDialogsById('storiesDialog').close();
			return;
		}

		if (index < 0) {
			index = 0;
		}

		const activeStory = this.stories.get(this.activeStoryIndex);

		activeStory.root.removeEventListener('pointerdown', this.onPointerDown);

		this.activeStoryIndex = index;

		this.root.go(this.activeStoryIndex);

		this.root.options.speed = 400;

		const currentStory = this.stories.get(index);

		// currentStory.go(0);

		currentStory.Components.Autoplay.play();

		currentStory.root.addEventListener('pointerdown', this.onPointerDown);

		this.stories.get(index).on('autoplay:playing', this.updateProgress);
		this.stories.get(index).on('moved', this.setupProgressbars);
	}

	hasVideo(slide) {
		return slide.querySelector('video');
	}

	getSlideInterval = (slideEl) => {
		const video = slideEl.querySelector('video');
		if (video) {
			return (video.duration || 5) * 1000; // fallback на 3s
		}
		return 5_000; // по умолчанию
	};

	setSlideInterval = () => {
		const activeStory = this.stories.get(this.activeStoryIndex);
		const activeSlideIndex = activeStory.index;

		const activeSlide =
			activeStory.Components.Slides.get()[activeSlideIndex].slide;

		console.log(this.getSlideInterval(activeSlide));

		activeStory.options.interval = this.getSlideInterval(activeSlide);
	};

	updateProgress = (rate) => {
		const activeStory = this.stories.get(this.activeStoryIndex);
		const activePaginationButton =
			activeStory.Components.Pagination.items[activeStory.index].button;

		activePaginationButton.style.setProperty('--progress', rate);

		if (rate === 1 && activeStory.index === activeStory.length - 1) {
			activeStory.off('autoplay:playing', this.updateProgress);
			this.openStory(this.activeStoryIndex + 1);
		}
	};
}
