export function lockFocus(element) {
	element.setAttribute('data-focus-trap', 'true');

	const focusableElements = element.querySelectorAll(
		'a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex^="-"])'
	);
	const firstFocusableElement = focusableElements[0];
	const lastFocusableElement = focusableElements[focusableElements.length - 1];

	firstFocusableElement.focus();

	function handleKeyDown(event) {
		if (event.key === 'Tab') {
			element.removeAttribute('data-focus-trap');

			if (event.shiftKey) {
				if (document.activeElement === firstFocusableElement) {
					lastFocusableElement.focus();
					event.preventDefault();
				}
			} else {
				if (document.activeElement === lastFocusableElement) {
					firstFocusableElement.focus();
					event.preventDefault();
				}
			}
		}
	}

	document.addEventListener('keydown', handleKeyDown);

	return () => {
		element.removeAttribute('data-focus-trap');
		document.removeEventListener('keydown', handleKeyDown);
	};
}

export function getCookie(name) {
	let matches = document.cookie.match(
		new RegExp(
			'(?:^|; )' +
				name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') +
				'=([^;]*)'
		)
	);
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

export function setCookie(name, value, options = {}) {
	options = {
		path: '/',
		// при необходимости добавьте другие значения по умолчанию
		...options,
	};

	if (options.expires instanceof Date) {
		options.expires = options.expires.toUTCString();
	}

	let updatedCookie =
		encodeURIComponent(name) + '=' + encodeURIComponent(value);

	for (let optionKey in options) {
		updatedCookie += '; ' + optionKey;
		let optionValue = options[optionKey];
		if (optionValue !== true) {
			updatedCookie += '=' + optionValue;
		}
	}

	document.cookie = updatedCookie;
}

export function deleteCookie(name) {
	setCookie(name, '', {
		'max-age': -1,
	});
}

export function debounce(func, ms) {
	let timeout;
	return function () {
		clearTimeout(timeout);
		timeout = setTimeout(() => func.apply(this, arguments), ms);
	};
}

export const copyTextToClipboard = (text) => {
	// Modern browsers
	if (navigator.clipboard && window.isSecureContext) {
		return navigator.clipboard.writeText(text);
	}

	// Fallback for older browsers
	const textArea = document.createElement('textarea');
	textArea.value = text;
	textArea.style.position = 'fixed';
	textArea.style.left = '-999999px';
	textArea.style.top = '-999999px';
	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	try {
		document.execCommand('copy');
		textArea.remove();
		return Promise.resolve();
	} catch (error) {
		textArea.remove();
		return Promise.reject(error);
	}
};

export const wait = (ms) => new Promise((res) => setTimeout(() => res(), ms));

export function formatDateToRussian(dateString) {
	const date = new Date(
		dateString.replace(
			/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/,
			'$1/$2/$3 $4:$5'
		)
	);

	const weekday = new Intl.DateTimeFormat('ru-RU', { weekday: 'long' }).format(
		date
	);
	const dateFormatter = new Intl.DateTimeFormat('ru-RU', {
		day: 'numeric',
		month: 'long',
	});
	const timeFormatter = new Intl.DateTimeFormat('ru-RU', {
		hour: '2-digit',
		minute: '2-digit',
	});

	const formattedDate = dateFormatter.format(date);
	const formattedTime = timeFormatter.format(date);

	return `${formattedDate}, ${weekday}, в ${formattedTime}`;
}

export function formatPrice(value, currency = 'RUB', locale = 'ru-RU') {
	return new Intl.NumberFormat(locale, {
		style: 'currency',
		currency,
		minimumFractionDigits: 0,
		maximumFractionDigits: 0,
	}).format(value);
}

/**
 *
 * @param {Promise<any>} promise
 */
export const promiseWrapper = async (promise) => {
	const [{ value, reason }] = await Promise.allSettled([promise]);
	return {
		data: value,
		error: reason,
	};
};

export const pxToRem = (pixels) => {
	return pixels / 16;
};

export const isSafariBrowser = () => {
	return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
};

/**
 *
 * @param {FormData} formData
 * @returns {Object}
 */
export const formDataToObject = (formData) => {
	const formDataObject = {};

	for (const [key, value] of formData) {
		if (key.endsWith('[]')) {
			formDataObject[key.slice(0, -2)] = formData.getAll(key);
		} else {
			formDataObject[key] = value;
		}
	}

	return formDataObject;
};

export const isValidRussianPhone = (phone) => {
	const cleaned = phone.replace(/[^\d+]/g, '');
	if (cleaned.startsWith('+7')) {
		return /^\+7\d{10}$/.test(cleaned);
	}
	if (cleaned.startsWith('8')) {
		return /^8\d{10}$/.test(cleaned);
	}
	return false;
};

/**
 * Плавный скролл к элементу с контролем изинга и длительности
 *
 * @param {Element} targetElement - DOM элемент, до которого нужно доскроллить
 * @param {Object} [config]
 * @param {number} [config.duration=600] - Длительность анимации в мс
 * @param {(t:number)=>number} [config.easing] - Функция изинга (0..1 → 0..1)
 * @param {'start'|'center'|'end'} [config.align='start'] - Куда выровнять элемент
 * @returns {Promise<void>}
 */
export function scrollToElement(targetElement, config = {}) {
	return new Promise((resolve, reject) => {
		if (!(targetElement instanceof Element)) {
			reject(
				new Error('scrollToElement: targetElement должен быть DOM-элементом.')
			);
			return;
		}

		const {
			duration = 600,
			easing = (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2), // easeInOutQuad по умолчанию
			align = 'start',
		} = config;

		const startY = window.pageYOffset;
		const rect = targetElement.getBoundingClientRect();
		let targetY;
		const scrollPaddingTop =
			parseInt(window.getComputedStyle(targetElement).scrollPaddingTop) || 0;

		switch (align) {
			case 'center':
				targetY = rect.top + startY - window.innerHeight / 2 + rect.height / 2;
				break;
			case 'end':
				targetY = rect.top + startY - window.innerHeight + rect.height;
				break;
			case 'start':
			default:
				targetY = rect.top + startY - scrollPaddingTop / 2;
				break;
		}

		const diff = targetY - startY;
		const startTime = performance.now();

		function step(currentTime) {
			const elapsed = currentTime - startTime;
			const progress = Math.min(elapsed / duration, 1);
			const easedProgress = easing(progress);

			window.scrollTo(0, startY + diff * easedProgress);

			if (elapsed < duration) {
				requestAnimationFrame(step);
			} else {
				resolve();
			}
		}

		requestAnimationFrame(step);
	});
}
