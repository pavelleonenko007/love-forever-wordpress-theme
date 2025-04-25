const ROOT_SELECTOR = "[data-js-timer]";

class SaleTimer {
  selectors = {
    root: ROOT_SELECTOR,
    days: "[data-js-timer-days]",
    hours: "[data-js-timer-hours]",
    minutes: "[data-js-timer-minutes]",
    seconds: "[data-js-timer-seconds]",
    dayWord: "[data-js-timer-days-word]",
    hourWord: "[data-js-timer-hours-word]",
    minuteWord: "[data-js-timer-minutes-word]",
    secondsWord: "[data-js-timer-seconds-word]",
  };

  /**
   * @param {HTMLElement} element
   */
  constructor(element) {
    this.root = element;

    this.daysElement = this.root.querySelector(this.selectors.days);
    this.hoursElement = this.root.querySelector(this.selectors.hours);
    this.minutesElement = this.root.querySelector(this.selectors.minutes);
    this.secondsElement = this.root.querySelector(this.selectors.seconds);

    this.daysWordElement = this.root.querySelector(this.selectors.dayWord);
    this.hoursWordElement = this.root.querySelector(this.selectors.hourWord);
    this.minutesWordElement = this.root.querySelector(
      this.selectors.minuteWord,
    );
    this.secondsWordElement = this.root.querySelector(
      this.selectors.secondsWord,
    );

    this.deadline = this.root.dataset.jsTimerDeadline
      ? new Date(parseInt(this.root.dataset.jsTimerDeadline) * 1_000)
      : new Date();

    this.timerId = setInterval(this.updateTimer, 1_000);
  }

  declensionNum(num, words) {
    return words[
      num % 100 > 4 && num % 100 < 20
        ? 2
        : [2, 0, 1, 1, 1, 2][num % 10 < 5 ? num % 10 : 5]
    ];
  }

  updateTimer = () => {
    const now = new Date();
    const diff = Math.max(0, this.deadline - now);

    console.log({ diff });

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    this.daysElement.textContent = String(days).padStart(2, "0");
    this.hoursElement.textContent = String(hours).padStart(2, "0");
    this.minutesElement.textContent = String(minutes).padStart(2, "0");
    this.secondsElement.textContent = String(seconds).padStart(2, "0");

    this.daysWordElement.textContent = this.declensionNum(days, [
      "день",
      "дня",
      "дней",
    ]);
    this.hoursWordElement.textContent = this.declensionNum(hours, [
      "час",
      "часа",
      "часов",
    ]);
    this.minutesWordElement.textContent = this.declensionNum(minutes, [
      "минута",
      "минуты",
      "минут",
    ]);
    this.secondsWordElement.textContent = this.declensionNum(seconds, [
      "секунда",
      "секунды",
      "секунд",
    ]);

    if (diff === 0) {
      clearInterval(this.timerId);
    }
  };

  destroy() {
    clearInterval(this.timerId);
  }
}

export class SaleTimerCollection {
  /**
   * @type {Map<HTMLElement, SaleTimer>}
   */
  static saleTimers = new Map();

  static init() {
    document.querySelectorAll(ROOT_SELECTOR).forEach((timerElement) => {
      const saleTimerInstance = new SaleTimer(timerElement);
      SaleTimerCollection.saleTimers.set(timerElement, saleTimerInstance);
    });
  }

  static destroyAll() {
    SaleTimerCollection.saleTimers.forEach((instance) => {
      instance.destroy();
    });

    SaleTimerCollection.saleTimers.clear();
  }
}
