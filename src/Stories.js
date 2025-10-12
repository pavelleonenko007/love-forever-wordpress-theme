import '@splidejs/splide/css/core';

import Splide from '@splidejs/splide';
import DialogCollection from './Dialog';

export class StoriesManager {
	constructor(element, sliderOptions = { startSlide: 0 }) {
		this.element = element;
		this.sliderOptions = sliderOptions;
		this.storiesDialog = DialogCollection.getDialogsById('storiesDialog');

		this.init();
		this.bindEvents();
	}

	init() {
		this.storiesSlider = new StoriesSlider(this.element, this.sliderOptions);
	}

	onEnded = () => {
		this.storiesDialog.close();
	};

	onCloseDialog = (event) => {
		const dialogId = event.detail.dialogId;
		if (dialogId === 'storiesDialog') {
			this.destroy();
		}
	};

	destroy() {
		this.storiesSlider.destroy();
		document.removeEventListener('stories:ended', this.onEnded);
		document.removeEventListener('dialogClose', this.onCloseDialog);
	}

	bindEvents() {
		document.addEventListener('stories:ended', this.onEnded);
		document.addEventListener('dialogClose', this.onCloseDialog);
	}
}

class StoriesSlider {
	constructor(element, options = { startSlide: 0 }) {
		this.element = element;
		this.activeStoryIndex = options.startSlide;

		this.init();
	}

	init() {
		this.stories = new Map();

		this.element.querySelectorAll('[data-js-story]').forEach((story, index) => {
			this.stories.set(index, new Story(story));
		});

		this.splide = new Splide(this.element, {
			width: '465rem',
			perMove: 1,
			perPage: 1,
			pagination: false,
			arrows: false,
			speed: 0,
			start: this.activeStoryIndex,
			drag: false,
			breakpoints: {
				479: {
					width: '100vw',
					padding: 0,
					gap: 0,
				},
			},
		});

		// Чтобы при открытии истории не было анимации, а после уже была
		setTimeout(() => {
			this.splide.options.speed = 400;
		}, 0);

		this.bindEvents();
		this.splide.mount();
		this.openStory(this.activeStoryIndex);
	}

	openStory(index) {
		if (index >= this.splide.length) {
			document.dispatchEvent(
				new CustomEvent('stories:ended', {
					bubbles: true,
				})
			);
			return;
		}

		if (index < 0) {
			index = 0;
			this.stories.get(this.activeStoryIndex).play();
		}

		this.splide.go(index);
	}

	next() {
		this.openStory(this.activeStoryIndex + 1);
	}

	prev() {
		this.openStory(this.activeStoryIndex - 1);
	}

	onActive = ({ index }) => {
		this.activeStoryIndex = index;

		this.stories.forEach((story, i) => {
			story.setIsActiveStory(i === this.activeStoryIndex);
		});

		this.stories.get(this.activeStoryIndex).play();
	};

	// onMoved = () => {
	// 	this.stories.get(this.activeStoryIndex).play();
	// };

	onMove = () => {
		this.stories.forEach((story) => {
			story.pause();
		});
	};

	onNext = () => {
		this.next();
	};

	onPrev = () => {
		this.prev();
	};

	onPointerUp = (event) => {
		const story = event.target.closest('[data-js-story]');

		if (!story) {
			return;
		}

		const storyIndex = parseInt(story.dataset.jsStory);

		if (storyIndex === this.activeStoryIndex) {
			return;
		}

		this.openStory(storyIndex);
	};

	bindEvents() {
		// this.element.addEventListener('pointerdown', this.onPointerDown);
		this.element.addEventListener('pointerup', this.onPointerUp);
		// this.splide.on('moved', this.onMoved);
		this.splide.on('move', this.onMove);
		this.splide.on('active', this.onActive);
		document.addEventListener('stories:next', this.onNext);
		document.addEventListener('stories:prev', this.onPrev);
	}

	destroy() {
		this.element.removeEventListener('pointerup', this.onPointerUp);
		this.splide.off('move', this.onMove);
		this.splide.off('active', this.onActive);
		document.removeEventListener('stories:next', this.onNext);
		document.removeEventListener('stories:prev', this.onPrev);
		this.splide.destroy();
		this.stories.forEach((story) => {
			story.destroy();
		});
		this.stories.clear();
	}
}

class Story {
	constructor(element, options = {}) {
		this.element = element;
		this.options = options;
		this.slides = new Map();
		/**
		 * @type {Map<number, ProgressBar>}
		 */
		this.progressBars = new Map();
		/**
		 * @type {Map<number, StoryPlayer>}
		 */
		this.storyPlayers = new Map();
		this.activeSlideIndex = 0;
		this.isActiveStory = false;
		this.lastPointerUpTime = 0;

		this.longPressTimer = null;
		this.isPausedByUser = false;
		this.longPressDelay = 600;

		this.init();
		this.bindEvents();

		this.loadSlideMedia(this.activeSlideIndex);

		this.openSlide(this.activeSlideIndex);
	}

	init() {
		this.element
			.querySelectorAll('[data-js-story-progress-bar]')
			.forEach((progressBar, index) => {
				this.progressBars.set(index, new ProgressBar(progressBar));
			});

		this.element
			.querySelectorAll('[data-js-story-slide]')
			.forEach((slide, index) => {
				this.slides.set(index, slide);
				this.storyPlayers.set(
					index,
					new StoryPlayer(slide, { progressBar: this.progressBars.get(index) })
				);
			});
	}

	setIsActiveStory(isActive) {
		this.isActiveStory = isActive;
	}

	openSlide(index) {
		if (index >= this.slides.size) {
			document.dispatchEvent(
				new CustomEvent('stories:next', {
					bubbles: true,
				})
			);
			return;
		}

		if (index < 0) {
			document.dispatchEvent(
				new CustomEvent('stories:prev', {
					bubbles: true,
				})
			);
			return;
		}

		this.activeSlideIndex = index;
		this.progressBars.forEach((progressBar, i) => {
			if (i > this.activeSlideIndex) {
				progressBar.setProgress(0);
			}

			if (i < this.activeSlideIndex) {
				progressBar.setProgress(1);
			}

			progressBar.getElement().classList.remove('is-active');
		});

		this.slides.forEach((slide, i) => {
			slide.classList.toggle('is-active', i === index);
		});

		let nextIndex = index + 1;

		if (nextIndex < this.slides.size) {
			this.loadSlideMedia(nextIndex);
		}
	}

	next() {
		console.log('next slide');

		this.progressBars
			.get(this.activeSlideIndex)
			.getElement()
			.classList.remove('is-active');
		this.stop();
		let nextIndex = this.activeSlideIndex + 1;

		if (nextIndex >= this.slides.size) {
			// Если это последний слайд, переходим к следующей истории
			document.dispatchEvent(
				new CustomEvent('stories:next', {
					bubbles: true,
				})
			);
			return;
		}

		this.openSlide(nextIndex);
		this.play();
	}

	prev() {
		this.reset();
		let prevIndex = this.activeSlideIndex - 1;

		if (prevIndex < 0) {
			// Если это первый слайд, переходим к предыдущей истории
			document.dispatchEvent(
				new CustomEvent('stories:prev', {
					bubbles: true,
				})
			);
			return;
		}

		this.openSlide(prevIndex);
		this.play();
	}

	pause = () => {
		const player = this.storyPlayers.get(this.activeSlideIndex);

		player.pause();
	};

	// Возобновить
	resume() {
		const player = this.storyPlayers.get(this.activeSlideIndex);
		player.resume();
	}

	stop() {
		const player = this.storyPlayers.get(this.activeSlideIndex);

		player.stop();
	}

	play() {
		const player = this.storyPlayers.get(this.activeSlideIndex);

		player?.play();
	}

	reset() {
		const player = this.storyPlayers.get(this.activeSlideIndex);

		player.reset();
	}

	loadSlideMedia(index) {
		const player = this.storyPlayers.get(index);

		player.loadSlideMedia();
	}

	onPointerDown = (event) => {
		if (!this.isActiveStory) return;

		// предотвращаем клик при удержании
		event.stopPropagation();

		// ставим таймер, если удерживает > 600 мс — ставим на паузу
		this.longPressTimer = setTimeout(() => {
			this.isPausedByUser = true;
			this.pause();
		}, this.longPressDelay);
	};

	onPointerUp = (event) => {
		console.log('onPointerUp');

		if (!this.isActiveStory) {
			return;
		}

		const now = performance.now();
		// 👇 защита от двойных pointerup в Safari
		if (now - this.lastPointerUpTime < 300) return;
		this.lastPointerUpTime = now;

		// Если удержание не успело сработать — просто очищаем таймер
		clearTimeout(this.longPressTimer);

		// Если история была на паузе — возобновляем
		if (this.isPausedByUser) {
			this.isPausedByUser = false;
			this.resume();
			return;
		}

		const rect = this.element.getBoundingClientRect();
		const x = event.clientX - rect.left;
		const width = rect.width;

		if (x < width / 2) {
			this.prev();
		} else {
			this.next();
		}
	};

	onNext = () => {
		if (!this.isActiveStory) {
			return;
		}

		this.next();
	};

	onPrev = () => {
		if (!this.isActiveStory) {
			return;
		}

		this.prev();
	};

	destroy() {
		this.storyPlayers.forEach((player) => {
			player.destroy();
		});
		this.storyPlayers.clear();
		this.progressBars.forEach((progressBar) => {
			progressBar.destroy();
		});
		this.progressBars.clear();

		this.element.removeEventListener('pointerdown', this.onPointerDown);
		this.element.removeEventListener('pointerup', this.onPointerUp);
		document.removeEventListener('story:next', this.onNext);
		document.removeEventListener('story:prev', this.onPrev);
		document.removeEventListener('stories:next', this.pause);
		document.removeEventListener('stories:prev', this.pause);
	}

	bindEvents() {
		this.element.addEventListener('pointerdown', this.onPointerDown);
		this.element.addEventListener('pointerup', this.onPointerUp);
		document.addEventListener('story:next', this.onNext);
		document.addEventListener('story:prev', this.onPrev);
		document.addEventListener('stories:next', this.pause);
		document.addEventListener('stories:prev', this.pause);
	}
}

class StoryPlayer {
	/**
	 * @param {HTMLElement} slide
	 * @param {{ progressBar: ProgressBar }} config
	 */
	constructor(slide, config = { progressBar: null }) {
		this.slide = slide;
		this.loader = this.slide.querySelector('[data-js-story-loader]');
		this.type = this.slide.dataset.jsStorySlideType;
		this.progressBar = config.progressBar;

		this.imageTimeout = null;
		this.remainingTime = 0;
		this.startTime = null;
		this.imageSlideDuration = 5_000;

		this.state = new Proxy(
			{
				isPlaying: false,
				isLoading: false,
				isLoaded: false,
			},
			{
				get: (target, prop) => {
					return target[prop];
				},
				set: (target, prop, value) => {
					const prevValue = target[prop];

					target[prop] = value;

					if (prevValue !== value) {
						this.updateUI();
					}

					return true;
				},
			}
		);
	}

	/**
	 *
	 * @param {HTMLVideoElement} video
	 */
	loadVideo(video) {
		return new Promise((resolve, reject) => {
			this.state.isLoading = true;

			if (video.querySelector('source')) {
				this.state.isLoading = false;
				this.state.isLoaded = true;

				resolve();
				return;
			}

			const src = video.getAttribute('data-src');
			if (!src) {
				throw new Error('No video source provided');
			}

			const source = document.createElement('source');
			source.src = src;
			source.type = 'video/mp4';
			video.appendChild(source);

			video.load();

			video.addEventListener(
				'canplay',
				() => {
					this.state.isLoading = false;
					this.state.isLoaded = true;

					resolve();
				},
				{ once: true }
			);
		});
	}

	loadImage(imageContainer) {
		return new Promise((resolve, reject) => {
			this.state.isLoading = true;

			if (imageContainer.querySelector('img')) {
				this.state.isLoading = false;
				this.state.isLoaded = true;

				resolve();
				return;
			}

			const src = imageContainer.getAttribute('data-src');
			const srcset = imageContainer.getAttribute('data-srcset');
			const sizes = imageContainer.getAttribute('data-sizes');
			const alt = imageContainer.getAttribute('data-alt') || '';

			if (!src) {
				throw new Error('No image source provided');
			}

			const image = new Image();

			imageContainer.appendChild(image);

			image.src = src;
			image.srcset = srcset;
			image.sizes = sizes;
			image.alt = alt;

			this.state.isLoading = true;

			image.onload = () => {
				this.state.isLoading = false;
				this.state.isLoaded = true;

				resolve();
			};

			image.onerror = () => {
				this.state.isLoading = false;
				this.state.isLoaded = false;
				reject(new Error('Failed to load image'));
			};
		});
	}

	async loadSlideMedia() {
		if (this.type === 'video') {
			await this.loadVideo(this.slide.querySelector('video[data-src]'));
		} else {
			await this.loadImage(this.slide.querySelector('div.story__bg'));
		}
	}

	async play() {
		if (this.type === 'video') {
			// Если не загружено — запускаем загрузку (но не ждем await)
			if (!this.state.isLoaded) {
				this.loadSlideMedia().then(() => {
					// Если пользователь уже "нажал" и видео в состоянии play, можно продолжить
					if (this.state.isPlaying) {
						this.playVideo();
					}
				});
			}

			// ⚡️ Запускаем видео сразу, пока Safari считает это user gesture
			this.playVideo();
		} else {
			await this.loadSlideMedia();
			this.playImage();
		}
	}

	playVideo() {
		const video = this.slide.querySelector('video');

		this.reset();

		this.progressBar.getElement().classList.add('is-active');

		video.ontimeupdate = () => {
			const progress = video.currentTime / video.duration;
			this.progressBar.setProgress(isNaN(progress) ? 0 : progress);
		};

		video.onended = () => {
			console.log('video onended', video, video.currentTime, video.duration);

			if (video.currentTime <= video.duration - 0.05) return; // safeguard for Safari
			this.progressBar.setProgress(1);
			document.dispatchEvent(
				new CustomEvent('story:next', {
					bubbles: true,
				})
			);
		};

		video.onplaying = () => {
			this.state.isLoading = false;
			this.state.isPlaying = true;
		};

		video.onpause = () => {
			this.state.isPlaying = false;
		};

		video.onwaiting = () => {
			this.state.isLoading = true;
		};

		video.onplay = () => {
			this.state.isLoading = false;
		};

		// 🔥 Критично для Safari:
		if (video.currentTime >= video.duration) {
			try {
				video.currentTime = 0; // гарантированно не вызовет onended
			} catch (e) {}
		}

		// 🔥 И не вызываем play синхронно после pointerup
		// чуть откладываем, чтобы Safari "отпустил" gesture
		setTimeout(() => {
			video.play().catch((err) => {
				console.warn('video play error (Safari workaround):', err);
			});
		}, 50);
	}

	playImage() {
		this.state.isPlaying = true;
		this.progressBar.reset();

		this.progressBar.animateTo(this.imageSlideDuration, () => {
			this.state.isPlaying = false;
			document.dispatchEvent(new CustomEvent('story:next', { bubbles: true }));
		});
	}

	pause() {
		if (this.type === 'video') {
			const video = this.slide.querySelector('video');
			video.pause();
			// video.currentTime = 0;
		} else {
			this.progressBar.stop();
		}
		this.state.isPlaying = false;
	}

	resume() {
		if (this.type === 'video') {
			const video = this.slide.querySelector('video');
			if (video.paused) {
				video.play();
			}
		} else {
			// Возобновляем анимацию с оставшегося прогресса
			const remaining =
				(1 - this.progressBar.progress) * this.imageSlideDuration;

			if (remaining > 0) {
				this.progressBar.animateTo(remaining, () => {
					this.state.isPlaying = false;
					document.dispatchEvent(
						new CustomEvent('story:next', { bubbles: true })
					);
				});
			}
		}
		this.state.isPlaying = true;
	}

	stop() {
		if (this.type === 'video') {
			const video = this.slide.querySelector('video');
			video.pause();
			video.currentTime = video.duration - 0.05;
		} else {
			this.progressBar.stop();
		}
	}

	reset() {
		if (this.type === 'video') {
			const video = this.slide.querySelector('video');
			video.pause();
			video.currentTime = 0;
		} else {
			this.progressBar.reset();
		}
	}

	destroy() {
		if (this.type === 'video') {
			const video = this.slide.querySelector('video');
			video.pause();
			video.innerHTML = '';
		} else {
			this.slide.querySelector('div.story__bg').innerHTML = '';
		}

		this.progressBar.destroy();
	}

	updateUI() {
		this.slide.classList.toggle('is-loading', this.state.isLoading);

		this.slide.classList.toggle('is-paused', !this.state.isPlaying);

		this.slide.classList.toggle('is-playing', this.state.isPlaying);
		this.progressBar.getElement().classList.toggle('is-playing', this.state.isPlaying);
	}
}

class ProgressBar {
	/**
	 *
	 * @param {HTMLElement} element
	 */
	constructor(element) {
		this.element = element;

		if (!this.element) {
			throw new Error('ProgressBar element not found');
		}

		this.progress = 0;
		this.animationFrame = null;
		this.startTime = null;
		this.duration = null;
		this.isPlaying = false;
		this.onComplete = null;

		this.setProgress(this.progress);
	}

	getElement() {
		return this.element;
	}

	setProgress(progress) {
		if (progress < 0) {
			progress = 0;
		}

		if (progress > 1) {
			progress = 1;
		}

		if (progress === this.progress) {
			return;
		}

		this.progress = progress;
		this.element.style.setProperty('--progress', progress * 100 + '%');
	}

	/**
	 * Запускает анимацию заполнения прогрессбара
	 * @param {number} duration — длительность в мс
	 * @param {Function} onComplete — колбэк после окончания
	 */
	animateTo(duration, onComplete = null) {
		this.stop(); // на случай повторного запуска

		this.startTime = performance.now();
		this.duration = duration;
		this.isPlaying = true;
		this.onComplete = onComplete;

		if (this.progress >= 1) {
			this.setProgress(1);
			if (typeof onComplete === 'function') onComplete();
			return;
		}

		const startProgress = this.progress; // важный момент — анимируем от текущего значения
		const remainingFraction = 1 - startProgress;
		// Защитный случай: если по каким-то причинам remainingFraction === 0
		if (remainingFraction <= 0) {
			this.setProgress(1);
			if (typeof onComplete === 'function') onComplete();
			return;
		}

		this.setProgress(startProgress);

		const tick = (now) => {
			if (!this.isPlaying) return;

			const elapsed = now - this.startTime;
			const frac = Math.min(elapsed / this.duration, 1);

			// newProgress = start + frac * remaining
			const newProgress = startProgress + frac * remainingFraction;

			this.setProgress(newProgress);

			if (frac < 1) {
				this.animationFrame = requestAnimationFrame(tick);
			} else {
				this.isPlaying = false;
				this.animationFrame = null;
				if (typeof this.onComplete === 'function') {
					this.onComplete();
				}
			}
		};

		this.animationFrame = requestAnimationFrame(tick);
	}

	/**
	 * Останавливает анимацию
	 */
	stop() {
		if (this.animationFrame) {
			cancelAnimationFrame(this.animationFrame);
			this.animationFrame = null;
		}
		this.isPlaying = false;
	}

	/**
	 * Сбрасывает прогресс
	 */
	reset() {
		this.stop();
		this.setProgress(0);
	}

	/**
	 * Уничтожает прогрессбар
	 */
	destroy() {
		this.stop();
		this.setProgress(0);
		this.onComplete = null;
		setTimeout(() => {
			this.element = null;
		}, 0);
	}
}
/**
 * Кастомный Autoplay для слайдеров с одним слайдом
 * Обновляет прогресс и переключает слайд по истечении интервала
 */
// class CustomAutoplay {
// 	constructor(splide, options = {}) {
// 		this.splide = splide;
// 		this.interval = options.interval || 5000;
// 		this.isPaused = true;
// 		this.startTime = 0;
// 		this.animationId = null;
// 		this.progress = 0;
// 		this.isMediaLoaded = false; // Флаг загрузки медиа

// 		// Callbacks
// 		this.onProgress = options.onProgress || (() => {});
// 		this.onComplete = options.onComplete || (() => {});
// 		this.onMediaLoaded = options.onMediaLoaded || (() => {});

// 		this.update = this.update.bind(this);
// 	}

// 	start() {
// 		if (!this.isPaused) return;

// 		// Запускаем автоплей только если медиа загружено
// 		if (!this.isMediaLoaded) {
// 			console.log('Autoplay paused: media not loaded yet');
// 			return;
// 		}

// 		this.isPaused = false;
// 		this.startTime = Date.now();
// 		this.progress = 0;
// 		this.animationId = requestAnimationFrame(this.update);
// 	}

// 	pause() {
// 		this.isPaused = true;
// 		if (this.animationId) {
// 			cancelAnimationFrame(this.animationId);
// 			this.animationId = null;
// 		}
// 	}

// 	rewind() {
// 		this.startTime = Date.now();
// 		this.progress = 0;
// 		this.onProgress(0);
// 	}

// 	/**
// 	 * Отмечает медиа как загруженное и запускает автоплей если он был приостановлен
// 	 */
// 	markMediaAsLoaded() {
// 		this.isMediaLoaded = true;
// 		this.onMediaLoaded();

// 		// Если автоплей был приостановлен из-за незагруженного медиа, запускаем его
// 		if (this.isPaused && !this.animationId) {
// 			this.start();
// 		}
// 	}

// 	/**
// 	 * Сбрасывает флаг загрузки медиа (при переходе к новому слайду)
// 	 */
// 	resetMediaLoaded() {
// 		this.isMediaLoaded = false;
// 	}

// 	set(interval) {
// 		this.interval = interval;
// 	}

// 	update() {
// 		if (this.isPaused) return;

// 		const elapsed = Date.now() - this.startTime;
// 		this.progress = Math.min(elapsed / this.interval, 1);

// 		// Обновляем прогресс
// 		this.onProgress(this.progress);

// 		// Если интервал завершен
// 		if (this.progress >= 1) {
// 			this.onComplete();
// 			this.rewind();
// 		}

// 		this.animationId = requestAnimationFrame(this.update);
// 	}

// 	destroy() {
// 		this.pause();
// 	}
// }

// const ROOT_SELECTOR = '[data-js-stories]';
// export default class Stories {
// 	selectors = {
// 		rootSlider: ROOT_SELECTOR,
// 		innerSlider: '[data-js-story]',
// 		storyCta: '[data-js-story-cta]',
// 	};

// 	constructor(element, options = { startSlide: 0 }) {
// 		this.element = element;
// 		this.stories = new Map();
// 		this.activeStoryIndex = options.startSlide;

// 		this.pressStartTime = 0;
// 		this.holdTimeout = null;

// 		this.holdDelay = 300;

// 		// Переменные для мониторинга прогресса
// 		this.lastProgressUpdate = null;
// 		this.lastProgressRate = null;

// 		// Хранилище кастомных автоплеев для слайдеров с одним слайдом
// 		this.customAutoplays = new Map();

// 		// Адаптивная предзагрузка
// 		this.isMobile = this.checkIsMobile();
// 		this.preloadedStories = new Set(); // Отслеживаем предзагруженные истории
// 		this.preloadedSlides = new Set(); // Отслеживаем предзагруженные слайды

// 		this.root = new Splide(this.element, {
// 			width: '465rem',
// 			perMove: 1,
// 			perPage: 1,
// 			pagination: false,
// 			arrows: false,
// 			speed: 0,
// 			drag: false,
// 			breakpoints: {
// 				479: {
// 					width: '100vw',
// 					padding: 0,
// 					gap: 0,
// 				},
// 			},
// 		});

// 		this.root.on('moved', this.onMovedRootSlider);
// 		this.root.on('move', this.onMoveRootSlider);
// 		this.root.on('move', this.updateRootSlidesScale);
// 		this.root.on('click', this.onRootClick);

// 		this.element
// 			.querySelectorAll(this.selectors.innerSlider)
// 			.forEach((element, index) => {
// 				const slides = element.querySelectorAll('.splide__slide');
// 				const hasMultipleSlides = slides.length > 1;

// 				const instance = new Splide(element, {
// 					perPage: 1,
// 					perMove: 1,
// 					type: 'fade',
// 					autoWidth: true,
// 					pagination: true,
// 					width: '465rem',
// 					arrows: false,
// 					autoplay: hasMultipleSlides, // Включаем автоплей только для множественных слайдов
// 					pauseOnHover: false,
// 					drag: false,
// 					// Принудительно включаем пагинацию даже для одного слайда
// 					classes: {
// 						pagination: 'splide__pagination',
// 						page: 'splide__pagination__page',
// 					},
// 				});

// 				this.stories.set(index, instance);

// 				instance.on('mounted', () => {
// 					// Для слайдеров с множественными слайдами используем стандартный автоплей
// 					if (hasMultipleSlides) {
// 						instance.Components.Autoplay.pause();
// 					} else {
// 						// Для слайдеров с одним слайдом создаем кастомный автоплей
// 						this.createCustomAutoplayForSingleSlide(instance, index);
// 					}
// 				});

// 				instance.mount();

// 				instance.on('move', (newIndex, prevIndex) => {
// 					const activeStory = this.stories.get(this.activeStoryIndex);
// 					const slides = activeStory.Components.Slides.get();

// 					for (let i = 0; i < slides.length; i++) {
// 						const splideSlide = slides[i];

// 						const slide = splideSlide.slide;
// 						const isActiveSlide = splideSlide.index === newIndex;

// 						// Загружаем медиа для активного слайда
// 						if (isActiveSlide) {
// 							this.loadSlideMedia(
// 								slide,
// 								this.activeStoryIndex,
// 								splideSlide.index
// 							)
// 								.then(() => {
// 									const video = this.hasVideo(slide);
// 									if (video) {
// 										this.handleVideoPlayback(video, slide, activeStory);
// 									}

// 									// Адаптивная предзагрузка после загрузки активного слайда
// 									if (this.isMobile) {
// 										// На мобильных предзагружаем следующий слайд
// 										this.preloadMobile(this.activeStoryIndex);
// 									}
// 								})
// 								.catch((error) => {
// 									console.warn('Failed to load slide media on move:', error);
// 									// При ошибке загрузки переходим к следующему слайду
// 									setTimeout(() => {
// 										activeStory.go('>');
// 									}, 2000);
// 								});
// 						} else {
// 							// Останавливаем видео в неактивных слайдах
// 							const video = this.hasVideo(slide);
// 							if (video) {
// 								video.pause();
// 								video.currentTime = 0;
// 								// Скрываем лоадер при паузе
// 								const loader = slide.querySelector('[data-js-video-loader]');
// 								if (loader) {
// 									loader.classList.remove('is-active');
// 								}
// 							}
// 						}
// 					}
// 				});

// 				instance.on('moved', () => {
// 					const activeStory = this.stories.get(this.activeStoryIndex);
// 					activeStory.Components.Autoplay.play();
// 					// Устанавливаем правильный интервал для нового активного слайда
// 					this.setSlideInterval();
// 				});
// 			});

// 		this.root.mount();

// 		this.openStory(this.activeStoryIndex);

// 		this.bindEvents();
// 	}

// 	bindEvents() {
// 		window.addEventListener('dialogClose', this.onDialogClose);
// 		window.addEventListener('resize', this.onWindowResize);
// 	}

// 	onDialogClose = () => {
// 		this.destroy();
// 	};

// 	onWindowResize = () => {
// 		const wasMobile = this.isMobile;
// 		this.isMobile = this.checkIsMobile();

// 		// Если изменился тип устройства, перезапускаем предзагрузку
// 		if (wasMobile !== this.isMobile) {
// 			this.adaptivePreload(this.activeStoryIndex);
// 		}
// 	};

// 	destroy() {
// 		window.removeEventListener('dialogClose', this.onDialogClose);
// 		window.removeEventListener('resize', this.onWindowResize);

// 		// Останавливаем все видео и сбрасываем таймеры перед уничтожением
// 		this.stopAllVideos();

// 		// Уничтожаем кастомные автоплеи
// 		this.customAutoplays.forEach((customAutoplay) => {
// 			customAutoplay.destroy();
// 		});
// 		this.customAutoplays.clear();

// 		// Очищаем кэш предзагрузки
// 		this.preloadedStories.clear();
// 		this.preloadedSlides.clear();

// 		this.root.destroy();
// 		this.stories.forEach((storySplide) => {
// 			storySplide.root.removeEventListener('pointerdown', this.onPointerDown);
// 			storySplide.destroy();
// 		});
// 		this.stories.clear();

// 		setTimeout(() => {
// 			this.root = null;
// 			this.element = null;
// 		});
// 	}

// 	onMoveRootSlider = () => {
// 		this.stories.forEach((splide, index) => {
// 			// Останавливаем стандартный автоплей
// 			splide.Components.Autoplay.pause();

// 			// Останавливаем кастомный автоплей если есть
// 			const customAutoplay = this.customAutoplays.get(index);
// 			if (customAutoplay) {
// 				customAutoplay.pause();
// 			}

// 			splide.Components.Slides.get().forEach((splideSlide) => {
// 				const video = this.hasVideo(splideSlide.slide);

// 				if (video) {
// 					video.pause();
// 					video.currentTime = 0;
// 				}
// 			});
// 		});
// 	};

// 	onMovedRootSlider = (newIndex, prevIndex, destIndex) => {
// 		// Автоплей запустится автоматически после загрузки медиа в playActiveSlideVideo
// 		// Не запускаем автоплей здесь, так как медиа может быть еще не загружено
// 		this.clearHold();
// 	};

// 	setupProgressbars = (newIndex) => {
// 		const activeStory = this.stories.get(this.activeStoryIndex);
// 		const { items } = activeStory.Components.Pagination;

// 		items.forEach((item, index) => {
// 			if (index > newIndex) {
// 				item.button.style.setProperty('--progress', 0);
// 			}

// 			if (index < newIndex) {
// 				item.button.style.setProperty('--progress', 1);
// 			}
// 		});
// 	};

// 	updateRootSlidesScale = (newIndex) => {
// 		const slides = this.root.Components.Slides.get();

// 		slides.forEach((slide, i) => {
// 			if (i === newIndex) {
// 				slide.slide.classList.add('is-active');
// 			} else {
// 				slide.slide.classList.remove('is-active');
// 			}
// 		});
// 	};

// 	onRootClick = (slide) => {
// 		setTimeout(() => {
// 			if (this.root.state.is(Splide.STATES.MOVING)) {
// 				return;
// 			}

// 			if (this.activeStoryIndex !== slide.index) {
// 				this.openStory(slide.index);
// 			}
// 		}, 0);
// 	};

// 	/**
// 	 *
// 	 * @param {PointerEvent} event
// 	 */
// 	onPointerDown = (event) => {
// 		console.log(event.target);

// 		if (
// 			event.target.closest(this.selectors.storyCta) ||
// 			event.target.closest('.splide__pagination__page')
// 		) {
// 			return;
// 		}

// 		event.preventDefault();
// 		event.stopPropagation();

// 		this.pressStartTime = Date.now();

// 		const activeStory = this.stories.get(this.activeStoryIndex);

// 		const { Autoplay } = activeStory.Components;
// 		const video = this.hasVideo(
// 			activeStory.Components.Slides.getAt(activeStory.index).slide
// 		);

// 		this.holdTimeout = setTimeout(() => {
// 			// Останавливаем автоплей (стандартный или кастомный)
// 			const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 			if (customAutoplay) {
// 				customAutoplay.pause();
// 			} else {
// 				Autoplay.pause();
// 			}

// 			if (video) {
// 				video.pause();
// 				video.currentTime = 0;
// 			}
// 		}, this.holdDelay);

// 		const onPointerUp = (event) => {
// 			this.clearHold();

// 			const pressDuration = Date.now() - this.pressStartTime;
// 			const containerWidth = activeStory.root.offsetWidth;

// 			if (pressDuration < this.holdDelay) {
// 				if (event.offsetX < containerWidth / 2) {
// 					if (activeStory.index === 0) {
// 						// Если это первый слайд первой истории, сбрасываем прогресс и перезапускаем
// 						if (this.activeStoryIndex === 0) {
// 							this.restartCurrentStory();
// 						} else {
// 							this.openStory(this.activeStoryIndex - 1);
// 						}
// 					} else {
// 						activeStory.go('<');
// 					}
// 				} else {
// 					if (activeStory.index === activeStory.length - 1) {
// 						this.openStory(this.activeStoryIndex + 1);
// 					} else {
// 						activeStory.go('>');
// 					}
// 				}
// 			} else {
// 				// Возобновляем автоплей (стандартный или кастомный) только если медиа загружено
// 				const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 				if (customAutoplay) {
// 					// Для кастомного автоплея проверяем загрузку медиа
// 					if (customAutoplay.isMediaLoaded) {
// 						customAutoplay.start();
// 					}
// 				} else {
// 					// Для стандартного автоплея запускаем только если медиа загружено
// 					const activeSlide = activeStory.Components.Slides.getAt(
// 						activeStory.index
// 					).slide;
// 					const hasMedia =
// 						activeSlide.querySelector('video[src]') ||
// 						activeSlide.querySelector('.story__bg--loaded img') ||
// 						(!activeSlide.querySelector('video[data-src]') &&
// 							!activeSlide.querySelector('.story__bg--placeholder[data-src]'));

// 					if (hasMedia) {
// 						Autoplay.play();
// 					}
// 				}
// 				video?.play();
// 			}
// 		};

// 		activeStory.root.addEventListener('pointerup', onPointerUp, {
// 			once: true,
// 		});
// 	};

// 	clearHold = () => {
// 		clearTimeout(this.holdTimeout);
// 		this.holdTimeout = null;
// 	};

// 	openStory(index = 0) {
// 		if (index >= this.root.length) {
// 			DialogCollection.getDialogsById('storiesDialog').close();
// 			return;
// 		}

// 		if (index < 0) {
// 			index = 0;
// 		}

// 		// Удаляем обработчики от предыдущей истории (если она существует)
// 		if (this.activeStoryIndex !== undefined) {
// 			const activeStory = this.stories.get(this.activeStoryIndex);
// 			if (activeStory) {
// 				activeStory.root.removeEventListener('pointerdown', this.onPointerDown);
// 				activeStory.off('autoplay:playing', this.updateProgress);
// 				activeStory.off('moved', this.setupProgressbars);
// 			}
// 		}

// 		this.activeStoryIndex = index;

// 		this.root.go(this.activeStoryIndex);

// 		this.root.options.speed = 400;

// 		const currentStory = this.stories.get(index);

// 		// currentStory.go(0);

// 		// Сбрасываем мониторинг прогресса для новой истории
// 		this.lastProgressUpdate = null;
// 		this.lastProgressRate = null;

// 		// Запускаем видео в активном слайде при открытии истории
// 		this.playActiveSlideVideo(currentStory);

// 		// Адаптивная предзагрузка медиа
// 		this.adaptivePreload(index);

// 		// Очищаем далекие истории для экономии памяти
// 		this.cleanupDistantStories(index);

// 		// Проверяем, есть ли кастомный автоплей для этой истории
// 		const customAutoplay = this.customAutoplays.get(index);
// 		if (customAutoplay) {
// 			// Для слайдеров с одним слайдом сбрасываем флаг загрузки медиа
// 			customAutoplay.resetMediaLoaded();
// 			// Автоплей запустится автоматически после загрузки медиа в playActiveSlideVideo
// 		} else {
// 			// Для слайдеров с множественными слайдами настраиваем интервал
// 			this.setSlideInterval();
// 			// Автоплей запустится автоматически после загрузки медиа в playActiveSlideVideo
// 			this.stories.get(index).on('autoplay:playing', this.updateProgress);
// 			this.stories.get(index).on('moved', this.setupProgressbars);
// 		}

// 		currentStory.root.addEventListener('pointerdown', this.onPointerDown);
// 	}

// 	hasVideo(slide) {
// 		return slide.querySelector('video');
// 	}

// 	/**
// 	 * Определяет, является ли устройство мобильным
// 	 * @returns {boolean} - true если мобильное устройство
// 	 */
// 	checkIsMobile() {
// 		return window.innerWidth < 768;
// 	}

// 	/**
// 	 * Получает уникальный идентификатор слайда
// 	 * @param {number} storyIndex - индекс истории
// 	 * @param {number} slideIndex - индекс слайда
// 	 * @returns {string} - уникальный ID
// 	 */
// 	getSlideId(storyIndex, slideIndex) {
// 		return `${storyIndex}-${slideIndex}`;
// 	}

// 	/**
// 	 * Проверяет, загружен ли слайд
// 	 * @param {number} storyIndex - индекс истории
// 	 * @param {number} slideIndex - индекс слайда
// 	 * @returns {boolean} - true если слайд загружен
// 	 */
// 	isSlidePreloaded(storyIndex, slideIndex) {
// 		return this.preloadedSlides.has(this.getSlideId(storyIndex, slideIndex));
// 	}

// 	/**
// 	 * Отмечает слайд как предзагруженный
// 	 * @param {number} storyIndex - индекс истории
// 	 * @param {number} slideIndex - индекс слайда
// 	 */
// 	markSlideAsPreloaded(storyIndex, slideIndex) {
// 		this.preloadedSlides.add(this.getSlideId(storyIndex, slideIndex));
// 	}

// 	/**
// 	 * Уведомляет кастомный автоплей о загрузке медиа
// 	 * @param {number} storyIndex - индекс истории
// 	 */
// 	notifyCustomAutoplayMediaLoaded(storyIndex) {
// 		const customAutoplay = this.customAutoplays.get(storyIndex);
// 		if (customAutoplay) {
// 			customAutoplay.markMediaAsLoaded();
// 		}
// 	}

// 	/**
// 	 * Управляет стандартным автоплеем Splide в зависимости от загрузки медиа
// 	 * @param {number} storyIndex - индекс истории
// 	 * @param {boolean} isMediaLoaded - загружено ли медиа
// 	 */
// 	manageStandardAutoplay(storyIndex, isMediaLoaded) {
// 		const story = this.stories.get(storyIndex);
// 		if (!story) return;

// 		if (isMediaLoaded) {
// 			// Если медиа загружено и это активная история, запускаем автоплей
// 			if (storyIndex === this.activeStoryIndex) {
// 				story.Components.Autoplay.play();
// 			}
// 		} else {
// 			// Если медиа не загружено, останавливаем автоплей
// 			story.Components.Autoplay.pause();
// 		}
// 	}

// 	/**
// 	 * Загружает медиа-файл (видео или изображение) для слайда
// 	 * @param {HTMLElement} slide - элемент слайда
// 	 * @param {number} storyIndex - индекс истории (опционально)
// 	 * @param {number} slideIndex - индекс слайда (опционально)
// 	 * @returns {Promise} - промис загрузки медиа
// 	 */
// 	loadSlideMedia(slide, storyIndex = null, slideIndex = null) {
// 		return new Promise((resolve, reject) => {
// 			// Проверяем, не загружен ли уже слайд
// 			if (
// 				storyIndex !== null &&
// 				slideIndex !== null &&
// 				this.isSlidePreloaded(storyIndex, slideIndex)
// 			) {
// 				// Если медиа уже загружено, уведомляем автоплей
// 				this.notifyCustomAutoplayMediaLoaded(storyIndex);
// 				this.manageStandardAutoplay(storyIndex, true);
// 				resolve();
// 				return;
// 			}

// 			// Останавливаем автоплей до загрузки медиа
// 			if (storyIndex !== null) {
// 				this.manageStandardAutoplay(storyIndex, false);
// 			}

// 			const video = slide.querySelector('video[data-src]');
// 			const imagePlaceholder = slide.querySelector(
// 				'.story__bg--placeholder[data-src]'
// 			);

// 			if (video) {
// 				this.loadVideo(video)
// 					.then(() => {
// 						if (storyIndex !== null && slideIndex !== null) {
// 							this.markSlideAsPreloaded(storyIndex, slideIndex);
// 						}
// 						// Уведомляем автоплей о загрузке видео
// 						this.notifyCustomAutoplayMediaLoaded(storyIndex);
// 						this.manageStandardAutoplay(storyIndex, true);
// 						resolve();
// 					})
// 					.catch(reject);
// 			} else if (imagePlaceholder) {
// 				this.loadImage(imagePlaceholder)
// 					.then(() => {
// 						if (storyIndex !== null && slideIndex !== null) {
// 							this.markSlideAsPreloaded(storyIndex, slideIndex);
// 						}
// 						// Уведомляем автоплей о загрузке изображения
// 						this.notifyCustomAutoplayMediaLoaded(storyIndex);
// 						this.manageStandardAutoplay(storyIndex, true);
// 						resolve();
// 					})
// 					.catch(reject);
// 			} else {
// 				// Если медиа уже загружено или отсутствует
// 				if (storyIndex !== null && slideIndex !== null) {
// 					this.markSlideAsPreloaded(storyIndex, slideIndex);
// 				}
// 				// Уведомляем автоплей о готовности медиа
// 				this.notifyCustomAutoplayMediaLoaded(storyIndex);
// 				this.manageStandardAutoplay(storyIndex, true);
// 				resolve();
// 			}
// 		});
// 	}

// 	/**
// 	 * Загружает видео для слайда
// 	 * @param {HTMLVideoElement} video - элемент видео
// 	 * @returns {Promise} - промис загрузки видео
// 	 */
// 	loadVideo(video) {
// 		return new Promise((resolve, reject) => {
// 			const src = video.getAttribute('data-src');
// 			if (!src) {
// 				reject(new Error('No video source provided'));
// 				return;
// 			}

// 			// Если видео уже загружено
// 			if (video.src) {
// 				resolve();
// 				return;
// 			}

// 			// Показываем лоадер
// 			const loader = video.parentElement.querySelector(
// 				'[data-js-video-loader]'
// 			);
// 			if (loader) {
// 				loader.classList.add('is-active');
// 			}

// 			// Создаем source элемент
// 			const source = document.createElement('source');
// 			source.src = src;
// 			source.type = 'video/mp4';
// 			video.appendChild(source);

// 			// Обработчики загрузки
// 			const handleLoad = () => {
// 				video.removeEventListener('canplay', handleLoad);
// 				video.removeEventListener('error', handleError);
// 				if (loader) {
// 					loader.classList.remove('is-active');
// 				}
// 				resolve();
// 			};

// 			const handleError = () => {
// 				video.removeEventListener('canplay', handleLoad);
// 				video.removeEventListener('error', handleError);
// 				if (loader) {
// 					loader.classList.remove('is-active');
// 				}
// 				reject(new Error('Failed to load video'));
// 			};

// 			video.addEventListener('canplay', handleLoad);
// 			video.addEventListener('error', handleError);

// 			// Загружаем видео
// 			video.load();
// 		});
// 	}

// 	/**
// 	 * Загружает изображение для слайда
// 	 * @param {HTMLElement} placeholder - элемент-плейсхолдер
// 	 * @returns {Promise} - промис загрузки изображения
// 	 */
// 	loadImage(placeholder) {
// 		return new Promise((resolve, reject) => {
// 			const src = placeholder.getAttribute('data-src');
// 			const srcset = placeholder.getAttribute('data-srcset');
// 			const sizes = placeholder.getAttribute('data-sizes');
// 			const alt = placeholder.getAttribute('data-alt') || '';

// 			if (!src) {
// 				reject(new Error('No image source provided'));
// 				return;
// 			}

// 			// Если изображение уже загружено
// 			if (placeholder.querySelector('img')) {
// 				resolve();
// 				return;
// 			}

// 			const img = new Image();

// 			img.onload = () => {
// 				// Заменяем плейсхолдер на изображение
// 				placeholder.innerHTML = '';
// 				placeholder.classList.remove('story__bg--placeholder');
// 				placeholder.classList.add('story__bg--loaded');
// 				img.className = 'story__bg';
// 				img.alt = alt;
// 				placeholder.appendChild(img);

// 				// Добавляем класс для плавного появления
// 				setTimeout(() => {
// 					img.classList.add('loaded');
// 					resolve();
// 				}, 50);
// 			};

// 			img.onerror = () => {
// 				// Показываем ошибку загрузки
// 				placeholder.innerHTML = `
// 					<div class="story__bg-error">
// 						<div class="story__bg-error-icon">⚠️</div>
// 						<div class="story__bg-error-text">Ошибка загрузки изображения</div>
// 					</div>
// 				`;
// 				placeholder.classList.add('story__bg--error');
// 				reject(new Error('Failed to load image'));
// 			};

// 			img.src = src;
// 			img.srcset = srcset;
// 			img.sizes = sizes;
// 		});
// 	}

// 	/**
// 	 * Обрабатывает воспроизведение видео в слайде
// 	 * @param {HTMLVideoElement} video - элемент видео
// 	 * @param {HTMLElement} slide - элемент слайда
// 	 * @param {Splide} activeStory - активная история
// 	 */
// 	handleVideoPlayback(video, slide, activeStory) {
// 		const loader = slide.querySelector('[data-js-video-loader]');

// 		// Добавляем мониторинг буферизации для переключенных видео
// 		if (!video.hasAttribute('data-buffering-monitor')) {
// 			let bufferingStartTime = null;
// 			let bufferingTimeout = null;

// 			const handleWaiting = () => {
// 				// Показываем лоадер при буферизации
// 				if (loader) {
// 					loader.classList.add('is-active');
// 				}

// 				if (!bufferingStartTime) {
// 					bufferingStartTime = Date.now();

// 					bufferingTimeout = setTimeout(() => {
// 						if (loader) {
// 							loader.classList.remove('is-active');
// 						}
// 						activeStory.go('>');
// 					}, 5000);
// 				}
// 			};

// 			const handleCanPlay = () => {
// 				// Скрываем лоадер когда видео готово
// 				if (loader) {
// 					loader.classList.remove('is-active');
// 				}

// 				if (bufferingStartTime) {
// 					bufferingStartTime = null;
// 					if (bufferingTimeout) {
// 						clearTimeout(bufferingTimeout);
// 						bufferingTimeout = null;
// 					}
// 				}

// 				// Обновляем интервал кастомного автоплея если это слайдер с одним слайдом
// 				const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 				if (customAutoplay) {
// 					this.updateCustomAutoplayInterval(this.activeStoryIndex);
// 				}
// 			};

// 			video.addEventListener('waiting', handleWaiting);
// 			video.addEventListener('canplay', handleCanPlay);
// 			video.setAttribute('data-buffering-monitor', 'true');
// 		}

// 		video.play().catch((error) => {
// 			// Скрываем лоадер при ошибке
// 			if (loader) {
// 				loader.classList.remove('is-active');
// 			}
// 			// Если видео не воспроизводится, переходим к следующему слайду
// 			setTimeout(() => {
// 				activeStory.go('>');
// 			}, 3000);
// 		});
// 	}

// 	/**
// 	 * Адаптивная предзагрузка медиа в зависимости от устройства
// 	 * @param {number} currentStoryIndex - индекс текущей истории
// 	 */
// 	adaptivePreload(currentStoryIndex) {
// 		if (this.isMobile) {
// 			this.preloadMobile(currentStoryIndex);
// 		} else {
// 			this.preloadDesktop(currentStoryIndex);
// 		}
// 	}

// 	/**
// 	 * Предзагрузка для мобильных устройств
// 	 * @param {number} currentStoryIndex - индекс текущей истории
// 	 */
// 	preloadMobile(currentStoryIndex) {
// 		const currentStory = this.stories.get(currentStoryIndex);
// 		if (!currentStory) return;

// 		const currentSlideIndex = currentStory.index;
// 		const slides = currentStory.Components.Slides.get();

// 		// Предзагружаем только следующий слайд в текущей истории
// 		const nextSlideIndex = currentSlideIndex + 1;
// 		if (nextSlideIndex < slides.length) {
// 			const nextSlide = slides[nextSlideIndex]?.slide;
// 			if (
// 				nextSlide &&
// 				!this.isSlidePreloaded(currentStoryIndex, nextSlideIndex)
// 			) {
// 				this.loadSlideMedia(nextSlide, currentStoryIndex, nextSlideIndex).catch(
// 					(error) => {
// 						console.warn('Failed to preload next slide on mobile:', error);
// 					}
// 				);
// 			}
// 		}
// 	}

// 	/**
// 	 * Предзагрузка для десктопных устройств
// 	 * @param {number} currentStoryIndex - индекс текущей истории
// 	 */
// 	preloadDesktop(currentStoryIndex) {
// 		// Предзагружаем первый слайд соседних историй
// 		const prevStoryIndex = currentStoryIndex - 1;
// 		const nextStoryIndex = currentStoryIndex + 1;

// 		// Предзагружаем предыдущую историю
// 		if (prevStoryIndex >= 0) {
// 			this.preloadStoryFirstSlide(prevStoryIndex);
// 		}

// 		// Предзагружаем следующую историю
// 		if (nextStoryIndex < this.root.length) {
// 			this.preloadStoryFirstSlide(nextStoryIndex);
// 		}

// 		// Загружаем все слайды текущей истории
// 		this.preloadCurrentStoryAllSlides(currentStoryIndex);
// 	}

// 	/**
// 	 * Предзагружает первый слайд истории
// 	 * @param {number} storyIndex - индекс истории
// 	 */
// 	preloadStoryFirstSlide(storyIndex) {
// 		if (this.preloadedStories.has(storyIndex)) return;

// 		const story = this.stories.get(storyIndex);
// 		if (!story) return;

// 		const slides = story.Components.Slides.get();
// 		if (slides.length > 0) {
// 			const firstSlide = slides[0]?.slide;
// 			if (firstSlide && !this.isSlidePreloaded(storyIndex, 0)) {
// 				this.loadSlideMedia(firstSlide, storyIndex, 0)
// 					.then(() => {
// 						this.preloadedStories.add(storyIndex);
// 					})
// 					.catch((error) => {
// 						console.warn(
// 							`Failed to preload first slide of story ${storyIndex}:`,
// 							error
// 						);
// 					});
// 			}
// 		}
// 	}

// 	/**
// 	 * Предзагружает все слайды текущей истории
// 	 * @param {number} storyIndex - индекс истории
// 	 */
// 	preloadCurrentStoryAllSlides(storyIndex) {
// 		const story = this.stories.get(storyIndex);
// 		if (!story) return;

// 		const slides = story.Components.Slides.get();
// 		slides.forEach((splideSlide, slideIndex) => {
// 			const slide = splideSlide.slide;
// 			if (slide && !this.isSlidePreloaded(storyIndex, slideIndex)) {
// 				this.loadSlideMedia(slide, storyIndex, slideIndex).catch((error) => {
// 					console.warn(
// 						`Failed to preload slide ${slideIndex} of story ${storyIndex}:`,
// 						error
// 					);
// 				});
// 			}
// 		});
// 	}

// 	/**
// 	 * Очищает предзагруженные медиа для историй, которые находятся далеко от текущей
// 	 * @param {number} currentStoryIndex - индекс текущей истории
// 	 */
// 	cleanupDistantStories(currentStoryIndex) {
// 		const keepRange = 1; // Держим в кэше текущую ±1 историю

// 		this.preloadedStories.forEach((storyIndex) => {
// 			if (Math.abs(storyIndex - currentStoryIndex) > keepRange) {
// 				this.cleanupStoryMedia(storyIndex);
// 				this.preloadedStories.delete(storyIndex);
// 			}
// 		});

// 		// Очищаем слайды далеких историй
// 		this.preloadedSlides.forEach((slideId) => {
// 			const [storyIndex] = slideId.split('-').map(Number);
// 			if (Math.abs(storyIndex - currentStoryIndex) > keepRange) {
// 				this.preloadedSlides.delete(slideId);
// 			}
// 		});
// 	}

// 	/**
// 	 * Очищает медиа истории
// 	 * @param {number} storyIndex - индекс истории
// 	 */
// 	cleanupStoryMedia(storyIndex) {
// 		const story = this.stories.get(storyIndex);
// 		if (!story) return;

// 		const slides = story.Components.Slides.get();
// 		slides.forEach((splideSlide) => {
// 			const slide = splideSlide.slide;
// 			const video = slide.querySelector('video');
// 			if (video) {
// 				video.pause();
// 				video.currentTime = 0;
// 				// Очищаем src для освобождения памяти
// 				video.removeAttribute('src');
// 				video.load();
// 			}
// 		});
// 	}

// 	/**
// 	 * Создает кастомный автоплей для слайдера с одним слайдом
// 	 * @param {Splide} instance - экземпляр Splide
// 	 * @param {number} index - индекс истории
// 	 */
// 	createCustomAutoplayForSingleSlide(instance, index) {
// 		const slide = instance.Components.Slides.get()[0]?.slide;
// 		if (!slide) return;

// 		const interval = this.getSlideInterval(slide);

// 		const customAutoplay = new CustomAutoplay(instance, {
// 			interval: interval,
// 			onProgress: (rate) => {
// 				// Обновляем прогресс-бар
// 				this.updateProgress(rate);
// 			},
// 			onComplete: () => {
// 				// Проверяем, является ли текущая история последней
// 				if (this.activeStoryIndex === this.root.length - 1) {
// 					// Если это последняя история, закрываем диалог
// 					DialogCollection.getDialogsById('storiesDialog').close();
// 				} else {
// 					// Переходим к следующей истории
// 					this.openStory(this.activeStoryIndex + 1);
// 				}
// 			},
// 			onMediaLoaded: () => {
// 				console.log(`Media loaded for story ${index}, starting autoplay`);
// 			},
// 		});

// 		// Сохраняем ссылку на кастомный автоплей
// 		this.customAutoplays.set(index, customAutoplay);

// 		// НЕ запускаем автоплей сразу - ждем загрузки медиа
// 		// Автоплей запустится автоматически после загрузки медиа в loadSlideMedia
// 	}

// 	/**
// 	 * Обновляет интервал кастомного автоплея для слайдера с одним слайдом
// 	 * Вызывается после загрузки видео для обновления интервала на длительность видео
// 	 * @param {number} index - индекс истории
// 	 */
// 	updateCustomAutoplayInterval(index) {
// 		const customAutoplay = this.customAutoplays.get(index);
// 		if (!customAutoplay) return;

// 		const instance = this.stories.get(index);
// 		const slide = instance.Components.Slides.get()[0]?.slide;
// 		if (!slide) return;

// 		const newInterval = this.getSlideInterval(slide);

// 		// Обновляем интервал и сбрасываем прогресс до 0
// 		customAutoplay.set(newInterval);
// 		customAutoplay.rewind();

// 		// Если это активная история, перезапускаем автоплей с новым интервалом
// 		if (index === this.activeStoryIndex) {
// 			customAutoplay.start();
// 		}
// 	}

// 	/**
// 	 * Перезапускает текущую историю с первого слайда
// 	 * Используется когда пользователь кликает на левую половину первого слайда первой истории
// 	 */
// 	restartCurrentStory() {
// 		const activeStory = this.stories.get(this.activeStoryIndex);

// 		// Останавливаем текущий автоплей
// 		const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 		if (customAutoplay) {
// 			customAutoplay.pause();
// 		} else {
// 			activeStory.Components.Autoplay.pause();
// 		}

// 		// Останавливаем и сбрасываем видео
// 		const slides = activeStory.Components.Slides.get();
// 		slides.forEach((splideSlide) => {
// 			const video = this.hasVideo(splideSlide.slide);
// 			if (video) {
// 				video.pause();
// 				video.currentTime = 0;
// 				// Скрываем лоадер
// 				const loader = splideSlide.slide.querySelector(
// 					'[data-js-video-loader]'
// 				);
// 				if (loader) {
// 					loader.classList.remove('is-active');
// 				}
// 			}
// 		});

// 		// Сбрасываем прогресс-бары
// 		const { items } = activeStory.Components.Pagination;
// 		items.forEach((item) => {
// 			item.button.style.setProperty('--progress', 0);
// 		});

// 		// Сбрасываем мониторинг прогресса
// 		this.lastProgressUpdate = null;
// 		this.lastProgressRate = null;

// 		// Переходим к первому слайду
// 		activeStory.go(0);

// 		// Запускаем видео в первом слайде
// 		this.playActiveSlideVideo(activeStory);

// 		// Автоплей запустится автоматически после загрузки медиа в playActiveSlideVideo
// 		// Не запускаем автоплей здесь, так как медиа может быть еще не загружено
// 	}

// 	/**
// 	 * Создает кастомный прогресс-бар для слайда
// 	 * @param {HTMLElement} slide - элемент слайда
// 	 * @param {number} rate - текущий прогресс (0-1)
// 	 */
// 	createCustomProgressBar(slide, rate) {
// 		// Проверяем, не создан ли уже прогресс-бар
// 		if (slide.querySelector('.story__progress-bar')) {
// 			return;
// 		}

// 		const progressBar = document.createElement('div');
// 		progressBar.className = 'story__progress-bar';
// 		progressBar.style.cssText = `
// 			position: absolute;
// 			top: 20rem;
// 			left: 20rem;
// 			right: 20rem;
// 			height: 4rem;
// 			background: rgba(255, 255, 255, 0.3);
// 			border-radius: 2rem;
// 			overflow: hidden;
// 			z-index: 5;
// 		`;

// 		const progressFill = document.createElement('div');
// 		progressFill.className = 'story__progress-fill';
// 		progressFill.style.cssText = `
// 			height: 100%;
// 			background: var(--white);
// 			border-radius: 2rem;
// 			width: ${rate * 100}%;
// 			transition: width 0.1s ease;
// 		`;

// 		progressBar.appendChild(progressFill);
// 		slide.appendChild(progressBar);
// 	}

// 	/**
// 	 * Останавливает все видео во всех историях и сбрасывает их таймеры
// 	 */
// 	stopAllVideos() {
// 		this.stories.forEach((storySplide, index) => {
// 			// Останавливаем стандартный автоплей
// 			storySplide.Components.Autoplay.pause();

// 			// Останавливаем кастомный автоплей если есть
// 			const customAutoplay = this.customAutoplays.get(index);
// 			if (customAutoplay) {
// 				customAutoplay.pause();
// 			}

// 			// Сбрасываем прогресс-бары
// 			const { items } = storySplide.Components.Pagination;
// 			items.forEach((item) => {
// 				item.button.style.setProperty('--progress', 0);
// 			});

// 			// Останавливаем все видео в слайдах
// 			const slides = storySplide.Components.Slides.get();
// 			slides.forEach((splideSlide) => {
// 				const video = this.hasVideo(splideSlide.slide);
// 				if (video) {
// 					video.pause();
// 					video.currentTime = 0;
// 				}
// 			});
// 		});
// 	}

// 	/**
// 	 * Запускает видео в активном слайде истории
// 	 * @param {Splide} storyInstance - экземпляр истории
// 	 */
// 	playActiveSlideVideo(storyInstance) {
// 		const activeSlideIndex = storyInstance.index;
// 		const activeSlide =
// 			storyInstance.Components.Slides.get()[activeSlideIndex]?.slide;

// 		if (!activeSlide) {
// 			console.warn('Active slide not found');
// 			return;
// 		}

// 		// Сначала загружаем медиа-файл для слайда
// 		this.loadSlideMedia(activeSlide, this.activeStoryIndex, activeSlideIndex)
// 			.then(() => {
// 				const video = this.hasVideo(activeSlide);

// 				if (video) {
// 					// Используем новый метод для обработки воспроизведения видео
// 					this.handleVideoPlayback(video, activeSlide, storyInstance);
// 				}

// 				// Обновляем интервал после успешной загрузки (для видео и изображений)
// 				const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 				if (customAutoplay) {
// 					// Для слайдеров с одним слайдом обновляем кастомный автоплей
// 					this.updateCustomAutoplayInterval(this.activeStoryIndex);
// 				} else {
// 					// Для слайдеров с множественными слайдами используем стандартный метод
// 					this.setSlideInterval();
// 				}
// 			})
// 			.catch((error) => {
// 				console.warn('Failed to load slide media:', error);
// 				// При ошибке загрузки медиа переходим к следующему слайду
// 				setTimeout(() => {
// 					storyInstance.go('>');
// 				}, 2000);
// 			});
// 	}

// 	getSlideInterval = (slideEl) => {
// 		const video = slideEl.querySelector('video');
// 		if (video) {
// 			const duration = video.duration;

// 			// Проверяем готовность видео
// 			if (
// 				video.readyState >= 1 &&
// 				duration &&
// 				!isNaN(duration) &&
// 				isFinite(duration) &&
// 				duration > 0
// 			) {
// 				return duration * 1000;
// 			}

// 			// Если видео есть, но метаданные не загружены, используем увеличенный fallback
// 			if (video.readyState === 0) {
// 				return 10_000; // 10 секунд для видео, которые еще загружаются
// 			}
// 		}
// 		return 5_000; // fallback на 5s для обычных слайдов
// 	};

// 	setSlideInterval = () => {
// 		const activeStory = this.stories.get(this.activeStoryIndex);
// 		const activeSlideIndex = activeStory.index;
// 		const activeSlide =
// 			activeStory.Components.Slides.get()[activeSlideIndex].slide;

// 		const newInterval = this.getSlideInterval(activeSlide);

// 		// Используем data-атрибут метод (более надежный)
// 		activeSlide.setAttribute('data-splide-interval', newInterval);
// 		activeStory.options.interval = newInterval;

// 		// Принудительно вызываем событие move для обновления интервала
// 		activeStory.emit('move', activeSlideIndex, activeSlideIndex);
// 	};

// 	updateProgress = (rate) => {
// 		const activeStory = this.stories.get(this.activeStoryIndex);
// 		const activePaginationButton =
// 			activeStory.Components.Pagination.items[activeStory.index]?.button;

// 		// Проверяем, есть ли кнопка пагинации
// 		if (activePaginationButton) {
// 			// Всегда обновляем прогресс-бар
// 			activePaginationButton.style.setProperty('--progress', rate);
// 		} else {
// 			// Альтернативный способ: ищем прогресс-бар в DOM
// 			const activeSlide =
// 				activeStory.Components.Slides.get()[activeStory.index]?.slide;
// 			if (activeSlide) {
// 				const customProgressBar = activeSlide.querySelector(
// 					'.story__progress-bar'
// 				);
// 				if (customProgressBar) {
// 					// Обновляем кастомный прогресс-бар
// 					const progressFill = customProgressBar.querySelector(
// 						'.story__progress-fill'
// 					);
// 					if (progressFill) {
// 						progressFill.style.width = `${rate * 100}%`;
// 					}
// 				} else {
// 					// Создаем кастомный прогресс-бар если его нет
// 					this.createCustomProgressBar(activeSlide, rate);
// 				}
// 			}
// 		}

// 		// Дополнительная проверка: если прогресс не движется больше 10 секунд
// 		// (это может указывать на зависшее видео)
// 		if (!this.lastProgressUpdate) {
// 			this.lastProgressUpdate = Date.now();
// 			this.lastProgressRate = rate;
// 		} else {
// 			const timeSinceLastUpdate = Date.now() - this.lastProgressUpdate;

// 			// Если прогресс не изменился больше 10 секунд и это не конец слайда
// 			if (
// 				timeSinceLastUpdate > 10000 &&
// 				rate === this.lastProgressRate &&
// 				rate < 0.95
// 			) {
// 				activeStory.go('>');
// 				return;
// 			}

// 			// Обновляем время последнего изменения прогресса
// 			if (rate !== this.lastProgressRate) {
// 				this.lastProgressUpdate = Date.now();
// 				this.lastProgressRate = rate;
// 			}
// 		}

// 		// Обработка завершения слайда
// 		if (rate === 1) {
// 			// Проверяем, есть ли кастомный автоплей (слайдер с одним слайдом)
// 			const customAutoplay = this.customAutoplays.get(this.activeStoryIndex);
// 			if (customAutoplay) {
// 				// Для слайдеров с одним слайдом переход к следующей истории уже обрабатывается в onComplete
// 				return;
// 			}

// 			// Для слайдеров с множественными слайдами
// 			// Если это последний слайд в истории
// 			if (activeStory.index === activeStory.length - 1) {
// 				// Если это последняя история, закрываем диалог
// 				if (this.activeStoryIndex === this.root.length - 1) {
// 					activeStory.off('autoplay:playing', this.updateProgress);
// 					DialogCollection.getDialogsById('storiesDialog').close();
// 				} else {
// 					// Переходим к следующей истории
// 					activeStory.off('autoplay:playing', this.updateProgress);
// 					this.openStory(this.activeStoryIndex + 1);
// 				}
// 			} else {
// 				// Переходим к следующему слайду в той же истории
// 				activeStory.go('>');
// 			}
// 		}
// 	};
// }
