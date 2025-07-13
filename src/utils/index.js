export function lockFocus(element) {
	const focusableElements = element.querySelectorAll(
		'a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex^="-"])'
	);
	const firstFocusableElement = focusableElements[0];
	const lastFocusableElement = focusableElements[focusableElements.length - 1];

	firstFocusableElement.focus();

	function handleKeyDown(event) {
		if (event.key === 'Tab') {
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

	return () => document.removeEventListener('keydown', handleKeyDown);
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
	console.log({ dateString });

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
