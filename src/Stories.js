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
		this.progressBar
			.getElement()
			.classList.toggle('is-playing', this.state.isPlaying);
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
