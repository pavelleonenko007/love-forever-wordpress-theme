import Splide from '@splidejs/splide';
import { AccordionCollection } from './Accordion';
import AddToFavoriteButtonCollection from './AddToFavoriteButton';
import CopyToClipboardButtonCollection from './CopyToClipboardButton';
import CustomSelectCollection from './CustomSelect';
import DeleteFittingButton from './DeleteFittingButton';
import DialogCollection from './Dialog';
import FavoritesButtonWithCounterCollection from './FavoritesButtonWithCounter';
import FileInputCollection from './FileInput';
import FilterFittingFormCollection from './FilterFittingForm';
import { FittingFormCollection } from './FittingForm';
import FormsValidator from './FormValidator';
import InputMaskCollection from './InputMask';
import MaskedPhoneButtonCollection from './MaskedPhoneButton';
import ProductFilterFormCollection from './ProductFilterForm';
import RangeSliderCollection from './RangeSlider';
import ReviewFormCollection from './ReviewForm';
import SearchFormCollection from './SearchForm';
import VideoPlayerCollection from './VideoPlayer';
import CardSliderCollection from './CardSlider';
import PlayIfVisibleVideoCollection from './PlayIfVisibleVideoCollection';
import { SaleTimerCollection } from './SaleTimer';
import CustomDatepickerCollection from './CustomDatePicker';
import Stories from './Stories';
import './styles/index.scss';
import { CallbackFormCollection } from './CallbackForm';
import InputZoomPrevention from './InputZoomPrevention';
import initMap from './YandexMap';
import ProductSliderCollection from './ProductSlider';
import CatalogSplideCollection from './CatalogSplide';
import ReviewImageSliderCollection from './ReviewImageSlider';
import FavoritesContactFormCollection from './FavoritesContactForm';

const mutationObserver = new MutationObserver((mutationRecords) => {
	for (let i = 0; i < mutationRecords.length; i++) {
		const isSliderDot =
			mutationRecords[i].target.classList.contains('w-slider-dot');

		if (!isSliderDot) continue;

		const sliderDot = mutationRecords[i].target;
		const isActiveDot = sliderDot.classList.contains('w-active');

		if (!isActiveDot) continue;

		const slider = sliderDot.closest('.slider_home-slider');
		const sliderDots = Array.from(slider.querySelectorAll('.w-slider-dot'));
		const activeSlideIndex = sliderDots.indexOf(sliderDot);

		if (activeSlideIndex === 0) {
			sliderDot.setAttribute('data-no-scale-x', '');

			setTimeout(() => {
				sliderDot.removeAttribute('data-no-scale-x');
			}, 20);
		}
	}
});

function initHeroPageSliderPagination() {
	document
		.querySelectorAll('.slider_home-slider.w-slider')
		.forEach((slider) => {
			mutationObserver.observe(slider, {
				attributes: true,
				attributeFilter: ['class'],
				attributeOldValue: true,
				subtree: true,
			});
		});
}

new FormsValidator();
new MaskedPhoneButtonCollection();

document.addEventListener('dialogOpen', (event) => {
	const dialogId = event.detail.dialogId;

	if (
		['singleProductFittingDialog', 'globalFittingDialog'].includes(dialogId)
	) {
		ym(43639474, 'reachGoal', 'FITTING_WIDGET_OPEN');
	}
});

document.addEventListener('favoritesUpdated', (event) => {
	console.log({ event });
	const { isActive, productId } = event.detail;

	if (!productId) {
		return;
	}

	if (!isActive) {
		ym(43639474, 'reachGoal', 'ADD_TO_FAVORITE');
	}
});

document.addEventListener('phoneNumberRevealed', (event) => {
	ym(43639474, 'reachGoal', 'PHONE_VIEW');
});

document.querySelectorAll('[href^="tel:"]').forEach((phoneLink) => {
	phoneLink.addEventListener('click', (event) => {
		ym(43639474, 'reachGoal', 'MOBILE_PHONE_CALL');
	});
});

// document.addEventListener('DOMContentLoaded', function () {
// 	Barba.Pjax.start();
// 	Barba.Prefetch.init();

// 	Barba.Pjax.originalPreventCheck = Barba.Pjax.preventCheck;

// 	Barba.Pjax.preventCheck = function (evt, element) {
// 		if (!Barba.Pjax.originalPreventCheck(evt, element)) {
// 			return false;
// 		}

// 		if (element.closest('#wpadminbar')) {
// 			return false;
// 		}

// 		return true;
// 	};

// 	var FadeTransition = Barba.BaseTransition.extend({
// 		start: function () {
// 			console.log('transition start');

// 			this.newContainerLoading
// 				.then(this.startTracking.bind(this))
// 				.then(this.fadeOut.bind(this))
// 				.then(this.fadeIn.bind(this));
// 		},
// 		fadeOut: function () {
// 			// RangeSliderCollection.destroyAll();
// 			return $(this.oldContainer)
// 				.animate({ visibility: 'visible' }, 100)
// 				.promise();
// 		},
// 		startTracking: function () {
// 			const productId = this.newContainer.dataset.productId;

// 			if (productId) {
// 				const formData = new FormData();
// 				formData.append('action', 'track_product_view');
// 				formData.append('product_id', productId);

// 				return fetch(`${window.origin}/wp-admin/admin-ajax.php`, {
// 					method: 'POST',
// 					credentials: 'same-origin',
// 					body: formData,
// 				})
// 					.then((response) => response.json())
// 					.then((data) => {
// 						console.log(data);
// 					})
// 					.catch((error) =>
// 						console.error('Error tracking product view:', error)
// 					);
// 			}

// 			return Promise.resolve();
// 		},
// 		fadeIn: function () {
// 			$(window).scrollTop(0);

// 			var _this = this;
// 			_this.done();

// 			console.log('done');

// 			FFFafterEnter();
// 			FFFafterLoad();

// 			Webflow.destroy();
// 			Webflow.ready();
// 			Webflow.require('ix2').init();
// 		},
// 	});
// 	Barba.Pjax.getTransition = function () {
// 		return FadeTransition;
// 	};

// 	Barba.Dispatcher.on('initStateChange', (currentStatus) => {
// 		console.log('initStateChange');

// 		closeMegaMenu();

// 		SearchFormCollection.destroyAll();
// 		FavoritesButtonWithCounterCollection.destroyAll();
// 		CopyToClipboardButtonCollection.destroyAll();
// 		CardSliderCollection.destroyAll();
// 		PlayIfVisibleVideoCollection.destroyAll();
// 		InputMaskCollection.destroyAll();
// 		DialogCollection.destroyAll();
// 		AccordionCollection.destroyAll();
// 		// FittingFormCollection.destroyAll();
// 		ProductFilterFormCollection.destroyAll();
// 		RangeSliderCollection.destroyAll();
// 		ReviewFormCollection.destroyAll();
// 		FileInputCollection.destroyAll();
// 		VideoPlayerCollection.destroyAll();
// 		CustomSelectCollection.destroyAll();
// 		FilterFittingFormCollection.destroyAll();
// 		SaleTimerCollection.destroyAll();
// 		CustomDatepickerCollection.destroyAll();
// 	});

// 	Barba.Dispatcher.on(
// 		'newPageReady',
// 		function (currentStatus, oldStatus, container, newPageRawHTML) {
// 			const match = newPageRawHTML.match(/data-wf-page="([^"]+)"/);
// 			const pageId = match ? match[1] : '';
// 			$('html').attr('data-wf-page', pageId);
// 		}
// 	);

// 	Barba.Dispatcher.on(
// 		'transitionCompleted',
// 		function (currentStatus, prevStatus) {
// 			document.documentElement.classList.remove('htmldopmenuopened');

// 			console.log('transitionCompleted');

// 			initPage();

// 			executeInlineScripts(document.querySelector('.barba-container'));

// 			switch (currentStatus.namespace) {
// 				case 'single-dress':
// 					initSingleDressPage();
// 					break;
// 				case 'catalog':
// 					initCatalogPage();
// 					break;
// 				case 'archive-review':
// 					initReviewsPage();
// 					break;
// 			}
// 		}
// 	);
// });

function executeInlineScripts(container) {
	const scripts = container.querySelectorAll('script');

	scripts.forEach((oldScript) => {
		const newScript = document.createElement('script');

		// Копируем атрибуты (например, type, data-* и т.д.)
		[...oldScript.attributes].forEach((attr) =>
			newScript.setAttribute(attr.name, attr.value)
		);

		// Если у скрипта src — создаем ссылку на внешний файл
		if (oldScript.src) {
			newScript.src = oldScript.src;
			newScript.async = oldScript.async;
		} else {
			// Для инлайновых скриптов — копируем содержимое
			newScript.textContent = oldScript.textContent;
		}

		// Вставляем и запускаем
		oldScript.parentNode.replaceChild(newScript, oldScript);
	});
}

function closeMegaMenu() {
	document.querySelectorAll('.lf-hover-menu').forEach((itemWithMegaMenu) => {
		itemWithMegaMenu.classList.remove('active');
	});
}

function initPage() {
	SearchFormCollection.init();
	PlayIfVisibleVideoCollection.init();
	initHeroPageSliderPagination();
	CardSliderCollection.init();
	FavoritesButtonWithCounterCollection.init();
	InputMaskCollection.init();
	DialogCollection.init();
	AccordionCollection.init();
	CopyToClipboardButtonCollection.init();
	VideoPlayerCollection.init();
	CustomSelectCollection.init();
	FittingFormCollection.init();
	FilterFittingFormCollection.init();
	SaleTimerCollection.init();
	CustomDatepickerCollection.init();
	CallbackFormCollection.init();
	ReviewImageSliderCollection.init();
	FavoritesContactFormCollection.init();
}

function initCatalogPage() {
	console.log('init catalog page');

	ProductFilterFormCollection.init();
	RangeSliderCollection.init();
}

function initSingleDressPage() {
	console.log('init single dress page');
	ProductSliderCollection.init();
}

function initReviewsPage() {
	console.log('init reviews page');
	ReviewFormCollection.init();
	FileInputCollection.init();
}

function initSearchResultsImageOnHover() {
	const searchResultsElements = document.querySelectorAll('.search-ajaxed');

	if (searchResultsElements.length < 1) {
		return;
	}
	// Функция для обновления позиции изображения
	const updateImagePosition = (parentItem, previewElement) => {
		previewElement.style.visibility = 'hidden'; // Скрываем, но сохраняем место в DOM
		previewElement.style.opacity = '0';
		previewElement.style.display = 'block'; // Делаем блочным, чтобы получить размеры

		const parentRect = parentItem.getBoundingClientRect();
		const previewRect = previewElement.getBoundingClientRect();

		if (previewRect.top < parentRect.top) {
			previewElement.style.translate = `0 ${Math.round(
				parentRect.top - previewRect.top
			)}px`;
		}

		// Возвращаем исходную видимость
		previewElement.style.visibility = 'visible';
		previewElement.style.opacity = '1';
	};

	const searchResultsObserver = new MutationObserver((mutationsList) => {
		for (let mutation of mutationsList) {
			if (mutation.type === 'childList') {
				mutation.addedNodes.forEach((searchResultElement) => {
					searchResultElement.onmouseenter = (event) => {
						const imagePreviewElement = searchResultElement.querySelector(
							'[data-js-search-result-image-preview]'
						);

						updateImagePosition(
							searchResultElement.parentElement,
							imagePreviewElement
						);

						searchResultElement.addEventListener(
							'mouseleave',
							() => {
								imagePreviewElement.style.display = 'none';
								imagePreviewElement.style.translate = null;
							},
							{
								once: true,
							}
						);
					};
				});
			}
		}
	});

	searchResultsElements.forEach((searchResultsElement) => {
		searchResultsObserver.observe(searchResultsElement, {
			subtree: true,
			attributes: true,
			childList: true,
		});
	});

	// document.body.addEventListener('mouseenter', (event) => {
	// 	console.log({ event });

	// 	if (!event.target.closest('[data-js-search-result]')) {
	// 		return;
	// 	}

	// 	/**
	// 	 * @type {HTMLElement}
	// 	 */
	// 	const searchResultElement = event.target.closest('[data-js-search-result]');
	// 	const imagePreviewElement = searchResultElement.querySelector(
	// 		'[data-js-search-result-image-preview]'
	// 	);

	// 	updateImagePosition(searchResultElement.parentElement, imagePreviewElement);

	// 	searchResultElement.addEventListener(
	// 		'mouseleave',
	// 		() => {
	// 			imagePreviewElement.style.display = 'none';
	// 			imagePreviewElement.style.translate = null;
	// 		},
	// 		{
	// 			once: true,
	// 		}
	// 	);
	// });
}

document.addEventListener('catalog:updated', () => {
	CardSliderCollection.destroyAll();
	CardSliderCollection.init();
});

document.addEventListener('DOMContentLoaded', () => {
	new AddToFavoriteButtonCollection();
	new DeleteFittingButton();
	initSearchResultsImageOnHover();

	initPage();
	initSingleDressPage();
	initCatalogPage();
	initReviewsPage();

	// switch (Barba.HistoryManager.currentStatus().namespace) {
	// 	case 'single-dress':
	// 		initSingleDressPage();
	// 	case 'catalog':
	// 		initCatalogPage();
	// 		break;
	// 	case 'archive-review':
	// 		initReviewsPage();
	// 		break;
	// }
});

document.addEventListener('DOMContentLoaded', (event) => {
	FFFafterEnter();
});

window.addEventListener('load', (event) => {
	FFFafterLoad();
});

window.addEventListener('dialogOpen', (event) => {
	const dialogId = event.detail.dialogId;

	if (dialogId === 'storiesDialog') {
		const storiesDialog = DialogCollection.getDialogsById('storiesDialog');
		const trigger = event.detail.trigger;
		const storyNumber = trigger.dataset.jsStoryItem
			? parseInt(trigger.dataset.jsStoryItem)
			: 0;

		const storiesElement =
			storiesDialog.dialog.querySelector('[data-js-stories]');

		new Stories(storiesElement, {
			startSlide: storyNumber,
		});
	}
});

function FFFafterEnter() {
	AllPages();
}

function FFFafterLoad() {
	if ($('.barba-container').hasClass('otzivs-page')) {
		otzivi();
	}

	if ($('.barba-container').hasClass('singlepage-page')) {
		singleblogpage();
	}
}

function AllPages() {
	initMap();
	// $('.playvideobtn').on('click', function () {
	// 	if ($(this).hasClass('videoplaing')) {
	// 		$(this).removeClass('videoplaing');
	// 		$(this).prev('video').get(0).pause();
	// 		//$(this).prev('video').get(0).removeAttr('controls');
	// 	} else {
	// 		$(this).addClass('videoplaing');
	// 		$(this).prev('video').get(0).play();
	// 		//$(this).prev('video').get(0).attr('controls');
	// 	}
	// });

	// Force browser reload on catalog link click with new query params and hash
	document.querySelectorAll('a[href$="#catalog"]').forEach((catalogLink) => {
		const url = new URL(catalogLink.href);
		const currentUrl = new URL(window.location.href);

		if (
			url.origin.replace(url.protocol, '') + url.pathname !==
			currentUrl.origin.replace(currentUrl.protocol, '') + currentUrl.pathname
		) {
			return;
		}

		const newHref = url.toString().replace('#catalog', '');

		catalogLink.href = newHref;

		catalogLink.addEventListener('click', (event) => {
			event.preventDefault();
			window.location.href = catalogLink.href + '#catalog';
		});
	});

	document
		.querySelectorAll('[data-js-input-zoom-prevention]')
		.forEach((inputZoomPrevention) => {
			new InputZoomPrevention(inputZoomPrevention);
		});

	const blogSplides = document.querySelectorAll('.splide.blog');

	blogSplides.forEach((splideBlogSlider) => {
		const splideBlogInstance = new Splide(splideBlogSlider, {
			// type: 'loop',
			perMove: 1,
			perPage: 1,
			pagination: false,
			arrows: true,
			focus: 'left',
			speed: 500,
			gap: '10rem',
			autoplay: false,
			interval: 2000,
			pagination: true,
			focus: 0,
			omitEnd: true,
			rewind: true,

			mediaQuery: 'min',
			breakpoints: {
				993: {
					destroy: true,
					perPage: 1,
				},
			},
		});

		splideBlogInstance.mount();
	});

	CatalogSplideCollection.init();

	// доп меню при наведении

	const navbar = document.querySelector('.navbar');
	const dropdownMenus = Array.from(document.querySelectorAll('.hovered-menue'));
	const hoverCloseMenus = dropdownMenus.map((dropdownMenu) =>
		dropdownMenu.querySelector('.hovered-menue_close-menu')
	);

	const openDropdown = (navLink) => {
		dropdownMenus.forEach((dropdown) => {
			dropdown.classList.remove('active');
		});

		document
			.querySelectorAll('.lf-icon-button--search')
			.forEach((iconButton) => {
				iconButton.classList.remove('is-active');
			});

		navLink.nextElementSibling.classList.add('active');

		navbar.classList.add('dopmenuopened');
		document.documentElement.classList.add('htmldopmenuopened');
	};

	const closeDropdown = () => {
		dropdownMenus.forEach((dropdown) => {
			dropdown.classList.remove('active');
		});

		navbar.classList.remove('dopmenuopened');

		document.documentElement.classList.remove('htmldopmenuopened');

		document
			.querySelectorAll('.lf-icon-button--search')
			.forEach((searchButton) => searchButton.classList.remove('is-active'));
	};

	hoverCloseMenus.forEach((hoverCloseMenu) => {
		hoverCloseMenu?.addEventListener('mouseenter', () => {
			closeDropdown();
		});
	});

	Array.from(document.querySelectorAll('.lf-nav-link'))
		.filter((navlink) => navlink.closest('.menu-link-keeper'))
		.forEach((navLink, index) => {
			let canBeOpened = !navLink.classList.contains('is-active');

			document.body.addEventListener(
				'mousemove',
				(event) => {
					if (!event.target.closest('.menu-link-keeper')) {
						canBeOpened = true;
					}
				},
				{
					once: true,
				}
			);
			/**
			 * @param {MouseEvent} event
			 */
			function onMouseEnter(event) {
				if (!canBeOpened) {
					return;
				}

				openDropdown(navLink);

				navLink.parentElement.addEventListener('mouseleave', onMouseLeave, {
					once: true,
				});
			}

			/**
			 * @param {MouseEvent} event
			 */
			function onMouseLeave(event) {
				closeDropdown();

				canBeOpened = true;

				navLink.addEventListener('mouseenter', onMouseEnter, {
					once: true,
				});
			}

			navLink.parentElement.addEventListener('mouseenter', onMouseEnter, {
				once: true,
			});

			navLink.parentElement.addEventListener('mouseleave', onMouseLeave, {
				once: true,
			});
		});
	// $('.n-menu').hover(function () {
	// 	if ($(this).parent().hasClass('menu-link-keeper')) {
	// 		$('.hovered-menue.active').removeClass('active');
	// 		$('.navbar').removeClass('dopmenuopened');
	// 		$(this).next().addClass('active');
	// 		$(this).closest('.navbar').addClass('dopmenuopened');
	// 		$('html').addClass('htmldopmenuopened');
	// 	}
	// });

	// $('.hovered-menue_close-menu').hover(function () {
	// 	$('.hovered-menue.active').removeClass('active');
	// 	$('.navbar').removeClass('dopmenuopened');
	// 	$('.lf-icon-button--search').removeClass('is-active');
	// 	$('html').removeClass('htmldopmenuopened');
	// });

	// открыть поиск по клику

	$('.lf-icon-button--search').click(function () {
		$('.menuline').removeClass('mobmenuopened');

		if ($(this).hasClass('is-active')) {
			$(this).removeClass('is-active');
			$('.hovered-menue.active').removeClass('active');
			$('.navbar').removeClass('dopmenuopened');
			$('html').removeClass('htmldopmenuopened');
			$('html').removeClass('is-locked');
			$(this).next().removeClass('active');
			$(this).closest('.navbar').removeClass('dopmenuopened');
		} else {
			$(this).addClass('is-active');
			$('.hovered-menue.active').removeClass('active');
			$('.navbar').removeClass('dopmenuopened');
			$('html').addClass('htmldopmenuopened');
			$('html').addClass('is-locked');
			$(this).next().addClass('active');
			$(this).closest('.navbar').addClass('dopmenuopened');
		}
	});

	$('.clear-search').click(function () {
		$('.search-input').val('');
	});

	$('.menu-bnt').click(function () {
		$('.dopmenuopened').removeClass('dopmenuopened');
		$('.serach-btn').removeClass('serach-open');
		$('.hovered-menue.active').removeClass('active');
		$('.lf-icon-button--search').removeClass('is-active');
		if ($(this).closest('.menuline').hasClass('mobmenuopened')) {
			$('html').removeClass('htmldopmenuopened');
			$('html').removeClass('is-locked');
			$(this).closest('.menuline').removeClass('mobmenuopened');
		} else {
			$('html').addClass('htmldopmenuopened');
			$('html').addClass('is-locked');
			$(this).closest('.menuline').addClass('mobmenuopened');
		}
	});

	$('.m-nav-drop').click(function () {
		var index = $(this).index();
		$(this)
			.closest('.mob-menu-kee')
			.find('.m-nav-content')
			.css('display', 'flex');
		$(this)
			.closest('.mob-menu-kee')
			.find('.m-nav-content')
			.children('.m-nav-content_in')
			.eq(index)
			.css('display', 'flex');
	});

	$('.m-nav-content_back').click(function () {
		$(this)
			.closest('.mob-menu-kee')
			.children('.m-nav-content')
			.css('display', 'none');
		$('.m-nav-content_in').css('display', 'none');
	});
}

function removeFilesItem(target) {
	let name = $(target).prev().text();
	let input = $(target).closest('.input-file-row').find('input[type=file]');
	$(target).closest('.input-file-list-item').remove();
	for (let i = 0; i < dt.items.length; i++) {
		if (name === dt.items[i].getAsFile().name) {
			dt.items.remove(i);
		}
	}
	input[0].files = dt.files;
}

function otzivi() {
	$('.slider-oyziv_nav').each(function () {
		var nums = $(this).find('div').length;
		$(this).append('<div class="slider-oyziv-last">&nbsp;/ ' + nums + '</div>');
	});
}

function singleblogpage() {
	$('.slider-oyziv_nav').each(function () {
		var nums = $(this).find('div').length;
		$(this).append('<div class="slider-oyziv-last">&nbsp;/ ' + nums + '</div>');
	});
}
