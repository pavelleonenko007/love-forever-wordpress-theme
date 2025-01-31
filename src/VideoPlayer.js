const ROOT_SELECTOR = '[data-js-video-player]';

class VideoPlayer {
	selectors = {
		root: ROOT_SELECTOR,
		video: '[data-js-video-player-video]',
		playButton: '[data-js-video-player-button]',
	};

	stateSelectors = {
		isPlaying: 'is-playing',
	};

	/**
	 *
	 * @param {HTMLElement} element
	 */
	constructor(element) {
		this.player = element;
		/**
		 * @type {HTMLVideoElement}
		 */
		this.video = this.player.querySelector(this.selectors.video);
		this.playButton = this.player.querySelector(this.selectors.playButton);

		this.onClick = this.onClick.bind(this);
		this.onPause = this.onPause.bind(this);

		this.bindEvents();
	}

	/**
	 *
	 * @param {PointerEvent} event
	 */
	onClick(event) {
		event.preventDefault();
		if (!this.video.paused) {
			return;
		}

		this.video.play().then(() => {
			this.player.classList.add(this.stateSelectors.isPlaying);
			this.video.controls = true;
		});
	}

	onPause() {
		this.player.classList.remove(this.stateSelectors.isPlaying);
		this.video.controls = false;
	}

	bindEvents() {
		this.playButton.addEventListener('click', this.onClick);
		this.video.addEventListener('pause', this.onPause);
	}

	destroy() {
		this.playButton.removeEventListener('click', this.onClick);
		this.video.removeEventListener('pause', this.onPause);
	}
}

export default class VideoPlayerCollection {
	/**
	 * @type {Map<HTMLElement, VideoPlayer>}
	 */
	static videoPlayers = new Map();

	static init() {
		document.querySelectorAll(ROOT_SELECTOR).forEach((videoPlayer) => {
			const videoPlayerInstance = new VideoPlayer(videoPlayer);
			this.videoPlayers.set(videoPlayer, videoPlayerInstance);
		});
	}

	static destroyAll() {
		this.videoPlayers.forEach((videoPlayer) => {
			videoPlayer.destroy();
		});
		this.videoPlayers.clear();
	}
}
