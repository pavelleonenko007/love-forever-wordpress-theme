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

		// Переменные для мониторинга прогресса
		this.lastProgressUpdate = null;
		this.lastProgressRate = null;

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
							const loader = splideSlide.slide.querySelector('[data-js-video-loader]');
							
							// Добавляем мониторинг буферизации для переключенных видео
							if (!video.hasAttribute('data-buffering-monitor')) {
								let bufferingStartTime = null;
								let bufferingTimeout = null;

								const handleWaiting = () => {
									// Показываем лоадер при буферизации
									if (loader) {
										loader.classList.add('is-active');
									}
									
									if (!bufferingStartTime) {
										bufferingStartTime = Date.now();
										console.log('Video started buffering in move event...');
										
										bufferingTimeout = setTimeout(() => {
											console.warn('Video buffering too long in move event, skipping to next slide');
											if (loader) {
												loader.classList.remove('is-active');
											}
											activeStory.go('>');
										}, 5000);
									}
								};

								const handleCanPlay = () => {
									// Скрываем лоадер когда видео готово
									if (loader) {
										loader.classList.remove('is-active');
									}
									
									if (bufferingStartTime) {
										const bufferingDuration = Date.now() - bufferingStartTime;
										console.log(`Video buffering completed in move event in ${bufferingDuration}ms`);
										
										bufferingStartTime = null;
										if (bufferingTimeout) {
											clearTimeout(bufferingTimeout);
											bufferingTimeout = null;
										}
									}
								};

								video.addEventListener('waiting', handleWaiting);
								video.addEventListener('canplay', handleCanPlay);
								video.setAttribute('data-buffering-monitor', 'true');
							}

							video.play().catch(error => {
								console.warn('Video play failed in move event:', error);
								// Скрываем лоадер при ошибке
								if (loader) {
									loader.classList.remove('is-active');
								}
								// Если видео не воспроизводится, переходим к следующему слайду
								setTimeout(() => {
									activeStory.go('>');
								}, 3000);
							});
						} else {
							video.pause();
							video.currentTime = 0;
							// Скрываем лоадер при паузе
							const loader = splideSlide.slide.querySelector('[data-js-video-loader]');
							if (loader) {
								loader.classList.remove('is-active');
							}
						}
					}
				});

				instance.on('moved', () => {
					const activeStory = this.stories.get(this.activeStoryIndex);
					activeStory.Components.Autoplay.play();
					// Устанавливаем правильный интервал для нового активного слайда
					this.setSlideInterval();
				});
			});

		this.root.mount();

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
		
		// Останавливаем все видео и сбрасываем таймеры перед уничтожением
		this.stopAllVideos();
		
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

		// ВАЖНО: Сначала устанавливаем правильный интервал, потом запускаем автоплей
		this.setSlideInterval();

		// Запускаем видео в активном слайде при открытии истории
		this.playActiveSlideVideo(currentStory);

		// Запускаем автоплей после установки правильного интервала
		currentStory.Components.Autoplay.play();

		currentStory.root.addEventListener('pointerdown', this.onPointerDown);

		this.stories.get(index).on('autoplay:playing', this.updateProgress);
		this.stories.get(index).on('moved', this.setupProgressbars);
		// Убираем дублирующий обработчик - setSlideInterval уже вызывается в openStory()
	}

	hasVideo(slide) {
		return slide.querySelector('video');
	}

	/**
	 * Останавливает все видео во всех историях и сбрасывает их таймеры
	 */
	stopAllVideos() {
		console.log('Stopping all videos and resetting timers');
		
		this.stories.forEach((storySplide) => {
			// Останавливаем автоплей
			storySplide.Components.Autoplay.pause();
			
			// Сбрасываем прогресс-бары
			const { items } = storySplide.Components.Pagination;
			items.forEach((item) => {
				item.button.style.setProperty('--progress', 0);
			});
			
			// Останавливаем все видео в слайдах
			const slides = storySplide.Components.Slides.get();
			slides.forEach((splideSlide) => {
				const video = this.hasVideo(splideSlide.slide);
				if (video) {
					video.pause();
					video.currentTime = 0;
					console.log('Video stopped and reset:', video);
				}
			});
		});
	}

	/**
	 * Запускает видео в активном слайде истории
	 * @param {Splide} storyInstance - экземпляр истории
	 */
	playActiveSlideVideo(storyInstance) {
		const activeSlideIndex = storyInstance.index;
		const activeSlide = storyInstance.Components.Slides.get()[activeSlideIndex]?.slide;
		
		if (!activeSlide) {
			console.warn('Active slide not found');
			return;
		}

		const video = this.hasVideo(activeSlide);
		const loader = activeSlide.querySelector('[data-js-video-loader]');
		
		if (video) {
			// Показываем лоадер при начале загрузки
			if (loader) {
				loader.classList.add('is-active');
			}

			// Добавляем обработчики для проблем с загрузкой
			const handleVideoError = () => {
				console.warn('Video failed to load, skipping to next slide');
				// Скрываем лоадер
				if (loader) {
					loader.classList.remove('is-active');
				}
				// Если видео не загрузилось, переходим к следующему слайду через 3 секунды
				setTimeout(() => {
					storyInstance.go('>');
				}, 3000);
			};

			const handleVideoLoad = () => {
				// Обновляем интервал после успешной загрузки
				this.setSlideInterval();
			};

			const handleCanPlayThrough = () => {
				// Видео полностью загружено и готово к воспроизведению
				if (loader) {
					loader.classList.remove('is-active');
				}
			};

			// Мониторинг буферизации
			let bufferingStartTime = null;
			let bufferingTimeout = null;

			const handleWaiting = () => {
				// Видео начало буферизацию - показываем лоадер
				if (loader) {
					loader.classList.add('is-active');
				}
				
				if (!bufferingStartTime) {
					bufferingStartTime = Date.now();
					console.log('Video started buffering...');
					
					// Если буферизация длится больше 5 секунд, переходим к следующему слайду
					bufferingTimeout = setTimeout(() => {
						console.warn('Video buffering too long, skipping to next slide');
						if (loader) {
							loader.classList.remove('is-active');
						}
						storyInstance.go('>');
					}, 5000);
				}
			};

			const handleCanPlay = () => {
				// Видео готово к воспроизведению - скрываем лоадер
				if (loader) {
					loader.classList.remove('is-active');
				}
				
				if (bufferingStartTime) {
					const bufferingDuration = Date.now() - bufferingStartTime;
					console.log(`Video buffering completed in ${bufferingDuration}ms`);
					
					// Сбрасываем таймеры
					bufferingStartTime = null;
					if (bufferingTimeout) {
						clearTimeout(bufferingTimeout);
						bufferingTimeout = null;
					}
				}
			};

			const handleStalled = () => {
				// Видео "застряло" при загрузке - показываем лоадер
				if (loader) {
					loader.classList.add('is-active');
				}
				
				console.warn('Video stalled, monitoring...');
				if (!bufferingStartTime) {
					bufferingStartTime = Date.now();
					
					// Если видео "застряло" больше 3 секунд, переходим к следующему
					bufferingTimeout = setTimeout(() => {
						console.warn('Video stalled too long, skipping to next slide');
						if (loader) {
							loader.classList.remove('is-active');
						}
						storyInstance.go('>');
					}, 3000);
				}
			};

			// Добавляем обработчики только если их еще нет
			if (!video.hasAttribute('data-error-handler')) {
				video.addEventListener('error', handleVideoError);
				video.addEventListener('loadedmetadata', handleVideoLoad);
				video.addEventListener('canplaythrough', handleCanPlayThrough);
				video.addEventListener('waiting', handleWaiting);
				video.addEventListener('canplay', handleCanPlay);
				video.addEventListener('stalled', handleStalled);
				video.setAttribute('data-error-handler', 'true');
			}

			// Запускаем видео с обработкой ошибок
			video.play().catch(error => {
				console.warn('Video play failed:', error);
				// Скрываем лоадер при ошибке
				if (loader) {
					loader.classList.remove('is-active');
				}
				// Если воспроизведение не удалось, переходим к следующему слайду
				setTimeout(() => {
					storyInstance.go('>');
				}, 3000);
			});
		}
	}

	getSlideInterval = (slideEl) => {
		const video = slideEl.querySelector('video');
		if (video) {
			const duration = video.duration;
			
			// Проверяем готовность видео
			if (video.readyState >= 1 && duration && !isNaN(duration) && isFinite(duration) && duration > 0) {
				return duration * 1000;
			}
			
			// Если видео есть, но метаданные не загружены, используем увеличенный fallback
			if (video.readyState === 0) {
				console.warn('Video metadata not loaded, using extended fallback');
				return 10_000; // 10 секунд для видео, которые еще загружаются
			}
		}
		return 5_000; // fallback на 5s для обычных слайдов
	};

	setSlideInterval = () => {
		const activeStory = this.stories.get(this.activeStoryIndex);
		const activeSlideIndex = activeStory.index;
		const activeSlide = activeStory.Components.Slides.get()[activeSlideIndex].slide;

		const newInterval = this.getSlideInterval(activeSlide);
		console.log('Setting slide interval to:', newInterval);

		// Используем data-атрибут метод (более надежный)
		activeSlide.setAttribute('data-splide-interval', newInterval);
		activeStory.options.interval = newInterval;
		
		// Принудительно вызываем событие move для обновления интервала
		activeStory.emit('move', activeSlideIndex, activeSlideIndex);
	};

	updateProgress = (rate) => {		
		const activeStory = this.stories.get(this.activeStoryIndex);
		const activePaginationButton =
			activeStory.Components.Pagination.items[activeStory.index].button;

		activePaginationButton.style.setProperty('--progress', rate);

		// Дополнительная проверка: если прогресс не движется больше 10 секунд
		// (это может указывать на зависшее видео)
		if (!this.lastProgressUpdate) {
			this.lastProgressUpdate = Date.now();
			this.lastProgressRate = rate;
		} else {
			const timeSinceLastUpdate = Date.now() - this.lastProgressUpdate;
			
			// Если прогресс не изменился больше 10 секунд и это не конец слайда
			if (timeSinceLastUpdate > 10000 && rate === this.lastProgressRate && rate < 0.95) {
				console.warn('Progress stuck, possible video issue, skipping to next slide');
				activeStory.go('>');
				return;
			}
			
			// Обновляем время последнего изменения прогресса
			if (rate !== this.lastProgressRate) {
				this.lastProgressUpdate = Date.now();
				this.lastProgressRate = rate;
			}
		}

		if (rate === 1 && activeStory.index === activeStory.length - 1) {
			activeStory.off('autoplay:playing', this.updateProgress);
			this.openStory(this.activeStoryIndex + 1);
		}
	};
}
