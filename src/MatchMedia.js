import { pxToRem } from './utils';

const MatchMedia = {
	mobile: window.matchMedia(`(width <= 767.98px)`),
	tablet: window.matchMedia(`(width <= 1024.98px)`),
};

export default MatchMedia;
