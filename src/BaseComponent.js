class BaseComponent {
	_getProxyState(initialState) {
		return new Proxy(initialState, {
			get: (target, prop) => {
				return target[prop];
			},
			set: (target, prop, newValue) => {
				// console.log(`Set ${newValue} to ${prop}`);

				const currentValue = target[prop];

				target[prop] = newValue;

				if (currentValue !== newValue) {
					this.updateUI();
				}

				return true;
			},
		});
	}

	updateUI() {
		throw new Error('Метод updateUI не реализован!');
	}
}

export default BaseComponent;
