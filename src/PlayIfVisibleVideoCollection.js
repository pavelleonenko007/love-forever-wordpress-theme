const ROOT_SELECTOR = "[data-js-play-if-visible-video]";

export default class PlayIfVisibleVideoCollection {
  static intersectionObserver = null;

  static init() {
    PlayIfVisibleVideoCollection.intersectionObserver = new IntersectionObserver(
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
      PlayIfVisibleVideoCollection.intersectionObserver.observe(video);
    });
  }

  static destroyAll() {
    PlayIfVisibleVideoCollection.intersectionObserver?.disconnect();
    PlayIfVisibleVideoCollection.intersectionObserver = null;
  }
}
