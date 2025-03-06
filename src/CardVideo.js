const ROOT_SELECTOR = "[data-js-card-video]";

export default class CardVideoCollection {
  static intersectionObserver = null;

  static init() {
    CardVideoCollection.intersectionObserver = new IntersectionObserver(
      (videos) => {
        videos.forEach((video) => {
          if (video.isIntersecting) {
            video.target.play();
          } else {
            video.target.pause();
          }
        });
      }
    );

    document.querySelectorAll(ROOT_SELECTOR).forEach((video) => {
      CardVideoCollection.intersectionObserver.observe(video);
    });
  }

  static destroyAll() {
    CardVideoCollection.intersectionObserver.disconnect();
    CardVideoCollection.intersectionObserver = null;
  }
}
