const ROOT_SELECTOR = '[data-js-file-input]';

class FileInput {
	selectors = {
		root: ROOT_SELECTOR,
		fileControl: '[data-js-file-input-control]',
		filesPreviewContainerElement: '[data-js-file-input-preview-container]',
		filePreviewElement: '[data-js-file-input-preview]',
		removeFileElement: '[data-js-file-input-remove-button]',
		fileName: '[data-js-file-input-file-name]',
	};

	/**
	 *
	 * @param {HTMLDivElement} element
	 */
	constructor(element) {
		this.root = element;
		this.fileControl = this.root.querySelector(this.selectors.fileControl);
		this.filesPreviewContainerElement = this.root.querySelector(
			this.selectors.filesPreviewContainerElement
		);
		this.fileList = new DataTransfer();

		this.onChange = this.onChange.bind(this);
		this.onClick = this.onClick.bind(this);

		this.bindEvents();
	}

	/**
	 *
	 * @param {File} file
	 */
	createFilePreview(file) {
		const reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onloadend = () => {
			const filePreviewElement = document.createElement('div');
			filePreviewElement.className = 'input-file-list-item';
			filePreviewElement.setAttribute('data-js-file-input-preview', '');

			const filePreviewHtml =
				'<img class="input-file-list-img" src="' +
				reader.result +
				'">' +
				'<span class="input-file-list-name" data-js-file-input-file-name>' +
				file.name +
				'</span>' +
				'<button type="button" data-js-file-input-remove-button class="input-file-list-remove" style="padding: 2rem; background-color: transparent;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="9" fill="white"/>  <circle cx="9" cy="9" r="8.5" stroke="black" stroke-opacity="0.1"/>  <line x1="12.6464" y1="12.3536" x2="5.64645" y2="5.35355" stroke="black"/>  <line y1="-0.5" x2="9.8995" y2="-0.5" transform="matrix(0.707107 -0.707107 -0.707107 -0.707107 5 12)" stroke="black"/></svg></button>';

			filePreviewElement.innerHTML = filePreviewHtml;
			this.filesPreviewContainerElement.appendChild(filePreviewElement);
		};
	}

	/**
	 *
	 * @param {number} index
	 */
	removeFileByIndex(index) {
		if (index === undefined || index < 0) {
			return;
		}

		this.fileList.items.remove(index);
		this.fileControl.files = this.fileList.files;

		this.filesPreviewContainerElement
			.querySelectorAll(this.selectors.filePreviewElement)
			[index].remove();
	}

	/**
	 *
	 * @param {Event} event
	 */
	onChange(event) {
		const files = Array.from(event.target.files);

		this.filesPreviewContainerElement.innerHTML = '';

		files.forEach((file) => {
			this.fileList.items.add(file);
			this.createFilePreview(file);
		});
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick(event) {
		if (event.target.closest(this.selectors.removeFileElement)) {
			event.preventDefault();

			const filePreviewElement = event.target.closest(
				this.selectors.filePreviewElement
			);
			const filePreviewIndex = Array.from(
				this.filesPreviewContainerElement.querySelectorAll(
					this.selectors.filePreviewElement
				)
			).indexOf(filePreviewElement);

			this.removeFileByIndex(filePreviewIndex);
		}
	}

	bindEvents() {
		this.fileControl.addEventListener('change', this.onChange);
		document.addEventListener('click', this.onClick);
	}

	destroy() {
		this.fileControl.removeEventListener('change', this.onChange);
		document.removeEventListener('click', this.onClick);

		this.fileList = null;
	}
}

export default class FileInputCollection {
	/**
	 * @type {Map<HTMLElement, FileInput>}
	 */
	static fileInputs = new Map();

	static destroyAll() {
		this.fileInputs.forEach((fileInput) => {
			fileInput.destroy();
		});
		this.fileInputs.clear();
	}

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((fileInput) => {
			this.fileInputs.set(fileInput, new FileInput(fileInput));
		});
	}
}
