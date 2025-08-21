class ViewportManager {
	constructor() {
		this.originalViewport = null;
		this.isViewportLocked = false;
		this.init();
	}

	init() {
		// Сохраняем оригинальный viewport
		const viewportMeta = document.querySelector('meta[name="viewport"]');
		if (viewportMeta) {
			this.originalViewport = viewportMeta.getAttribute('content');
		} else {
			// Если viewport meta tag не найден, создаем его
			this.originalViewport = 'width=device-width, initial-scale=1.0';
			const meta = document.createElement('meta');
			meta.name = 'viewport';
			meta.content = this.originalViewport;
			document.head.appendChild(meta);
		}
	}

	lockViewport() {
		if (this.isViewportLocked) return;
		
		const viewportMeta = document.querySelector('meta[name="viewport"]');
		if (viewportMeta) {
			viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
			this.isViewportLocked = true;
		}
	}

	unlockViewport() {
		if (!this.isViewportLocked) return;
		
		const viewportMeta = document.querySelector('meta[name="viewport"]');
		if (viewportMeta && this.originalViewport) {
			viewportMeta.setAttribute('content', this.originalViewport);
			this.isViewportLocked = false;
		}
	}
}

// Создаем глобальный экземпляр
export const viewportManager = new ViewportManager();