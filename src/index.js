import Splide from '@splidejs/splide';
import Barba from 'barba.js';
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
import './styles/index.scss';
import VideoPlayerCollection from './VideoPlayer';
import CardSliderCollection from './CardSlider';

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

document.addEventListener('DOMContentLoaded', function () {
	Barba.Pjax.start();
	Barba.Prefetch.init();

	Barba.Pjax.originalPreventCheck = Barba.Pjax.preventCheck;

	Barba.Pjax.preventCheck = function (evt, element) {
		if (!Barba.Pjax.originalPreventCheck(evt, element)) {
			return false;
		}

		if (element.closest('#wpadminbar')) {
			return false;
		}

		return true;
	};

	var FadeTransition = Barba.BaseTransition.extend({
		start: function () {
			console.log('transition start');

			this.newContainerLoading
				.then(this.startTracking.bind(this))
				.then(this.fadeOut.bind(this))
				.then(this.fadeIn.bind(this));
		},
		fadeOut: function () {
			// RangeSliderCollection.destroyAll();
			return $(this.oldContainer)
				.animate({ visibility: 'visible' }, 100)
				.promise();
		},
		startTracking: function () {
			const productId = this.newContainer.dataset.productId;

			if (productId) {
				const formData = new FormData();
				formData.append('action', 'track_product_view');
				formData.append('product_id', productId);

				return fetch(`${window.origin}/wp-admin/admin-ajax.php`, {
					method: 'POST',
					credentials: 'same-origin',
					body: formData,
				})
					.then((response) => response.json())
					.then((data) => {
						console.log(data);
					})
					.catch((error) =>
						console.error('Error tracking product view:', error)
					);
			}

			return Promise.resolve();
		},
		fadeIn: function () {
			$(window).scrollTop(0);

			var _this = this;
			_this.done();

			console.log('done');

			FFFafterEnter();
			FFFafterLoad();

			Webflow.destroy();
			Webflow.ready();
			Webflow.require('ix2').init();
		},
	});
	Barba.Pjax.getTransition = function () {
		return FadeTransition;
	};

	Barba.Dispatcher.on('initStateChange', (currentStatus) => {
		console.log('initStateChange');

		SearchFormCollection.destroyAll();
		FavoritesButtonWithCounterCollection.destroyAll();
		CopyToClipboardButtonCollection.destroyAll();
		CardSliderCollection.destroyAll();
		InputMaskCollection.destroyAll();
		DialogCollection.destroyAll();
		AccordionCollection.destroyAll();
		FittingFormCollection.destroyAll();
		ProductFilterFormCollection.destroyAll();
		RangeSliderCollection.destroyAll();
		ReviewFormCollection.destroyAll();
		FileInputCollection.destroyAll();
		VideoPlayerCollection.destroyAll();
		CustomSelectCollection.destroyAll();
		FilterFittingFormCollection.destroyAll();
	});

	Barba.Dispatcher.on(
		'newPageReady',
		function (currentStatus, oldStatus, container, newPageRawHTML) {
			const match = newPageRawHTML.match(/data-wf-page="([^"]+)"/);
			const pageId = match ? match[1] : '';
			$('html').attr('data-wf-page', pageId);
		}
	);

	Barba.Dispatcher.on(
		'transitionCompleted',
		function (currentStatus, prevStatus) {
			document.documentElement.classList.remove('htmldopmenuopened');

			initPage();

			switch (currentStatus.namespace) {
				case 'single-dress':
					initSingleDressPage();
					break;
				case 'catalog':
					initCatalogPage();
					break;
				case 'archive-review':
					initReviewsPage();
					break;
			}
		}
	);
});

function initPage() {
	console.log('init page');

	SearchFormCollection.init();
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
}

function initCatalogPage() {
	console.log('init catalog page');

	ProductFilterFormCollection.init();
	RangeSliderCollection.init();
}

function initSingleDressPage() {
	console.log('init single dress page');
}

function initReviewsPage() {
	console.log('init reviews page');
	ReviewFormCollection.init();
	FileInputCollection.init();
}

document.addEventListener('DOMContentLoaded', () => {
	console.log('load');

	new AddToFavoriteButtonCollection();
	new DeleteFittingButton();

	initPage();

	switch (Barba.HistoryManager.currentStatus().namespace) {
		case 'single-dress':
			initSingleDressPage();
		case 'catalog':
			initCatalogPage();
			break;
		case 'archive-review':
			initReviewsPage();
			break;
	}
});

document.addEventListener('DOMContentLoaded', (event) => {
	FFFafterEnter();
});

window.addEventListener('load', (event) => {
	FFFafterLoad();
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

	const splideGalleries = document.querySelectorAll('.splide');

	$('.splide-arrow')
		.eq(0)
		.on('click', function () {
			$(this)
				.closest('.container')
				.find('.splide__arrow.splide__arrow--prev')
				.click();
		});

	$('.splide-arrow')
		.eq(1)
		.on('click', function () {
			$(this)
				.closest('.container')
				.find('.splide__arrow.splide__arrow--next')
				.click();
		});

	splideGalleries.forEach((splideElement) => {
		if ($(splideElement).hasClass('blog')) {
			//	var datanum = $(splideElement).attr('data-nums');

			var splide = new Splide(splideElement, {
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
			splide.mount();
		} else if ($(splideElement).hasClass('no-pc')) {
			var splide = new Splide(splideElement, {
				// type: 'loop',
				perMove: 1,
				perPage: 2,
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
						perPage: 2,
					},
				},
			});
			splide.mount();
		} else {
			var splide = new Splide(splideElement, {
				// type: 'loop',
				perMove: 1,
				perPage: 4,
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
				breakpoints: {
					993: {
						perPage: 1,
					},
				},
			});
			splide.mount();
		}
	});

	// доп меню при наведении

	$('.n-menu').hover(function () {
		if ($(this).parent().hasClass('menu-link-keeper')) {
			$('.hovered-menue.active').removeClass('active');
			$('.navbar').removeClass('dopmenuopened');
			$(this).next().addClass('active');
			$(this).closest('.navbar').addClass('dopmenuopened');
			$('html').addClass('htmldopmenuopened');
		}
	});

	$('.hovered-menue_close-menu').hover(function () {
		$('.hovered-menue.active').removeClass('active');
		$('.navbar').removeClass('dopmenuopened');
		$('.serach-btn').removeClass('serach-open');
		$('html').removeClass('htmldopmenuopened');
	});

	// открыть поиск по клику

	$('.serach-btn').click(function () {
		$('.menuline').removeClass('mobmenuopened');

		if ($(this).hasClass('serach-open')) {
			$(this).removeClass('serach-open');
			$('.hovered-menue.active').removeClass('active');
			$('.navbar').removeClass('dopmenuopened');
			$('html').removeClass('htmldopmenuopened');
			$(this).next().removeClass('active');
			$(this).closest('.navbar').removeClass('dopmenuopened');
		} else {
			$(this).addClass('serach-open');
			$('.hovered-menue.active').removeClass('active');
			$('.navbar').removeClass('dopmenuopened');
			$('html').addClass('htmldopmenuopened');
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
		if ($(this).closest('.menuline').hasClass('mobmenuopened')) {
			$('html').removeClass('htmldopmenuopened');

			$(this).closest('.menuline').removeClass('mobmenuopened');
		} else {
			$('html').addClass('htmldopmenuopened');
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
	console.log('otzivi');
	$('.slider-oyziv_nav').each(function () {
		var nums = $(this).find('div').length;
		$(this).append('<div class="slider-oyziv-last">&nbsp;/ ' + nums + '</div>');
		console.log(nums);
	});
}

function singleblogpage() {
	$('.slider-oyziv_nav').each(function () {
		var nums = $(this).find('div').length;
		$(this).append('<div class="slider-oyziv-last">&nbsp;/ ' + nums + '</div>');
		console.log(nums);
	});
}
