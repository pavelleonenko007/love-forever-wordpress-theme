/******/ (() => { // webpackBootstrap
/*!****************************!*\
  !*** ./src/admin/index.js ***!
  \****************************/
const REGULAR_PRICE_FIELD_SELECTOR = '#regularPriceField input';
const DISCOUNT_PERCENT_FIELD_SELECTOR = '#salePercentField input';
const PRICE_WITH_DISCOUNT_FIELD_SELECTOR = '#priceWithDiscountField input';
jQuery(document).ready(function ($) {
  const $regularPriceField = $(REGULAR_PRICE_FIELD_SELECTOR);
  const $discountPercentField = $(DISCOUNT_PERCENT_FIELD_SELECTOR);
  const $priceWithDiscountField = $(PRICE_WITH_DISCOUNT_FIELD_SELECTOR);
  $discountPercentField.on('input', event => {
    const regularPrice = $regularPriceField.val() ? parseInt($regularPriceField.val()) : 0;
    const discountPercent = $discountPercentField.val() ? parseInt($discountPercentField.val()) : 0;
    if (discountPercent > 99) {
      $discountPercentField.val(99);
    }
    if (discountPercent < 0) {
      $discountPercentField.val(0);
    }
    const priceWithDiscount = regularPrice - regularPrice * discountPercent / 100;
    $priceWithDiscountField.val(Math.ceil(priceWithDiscount));
  });
  $priceWithDiscountField.on('input', event => {
    const regularPrice = $regularPriceField.val() ? parseInt($regularPriceField.val()) : 0;
    const priceWithDiscount = $priceWithDiscountField.val() ? parseInt($priceWithDiscountField.val()) : 0;
    if (priceWithDiscount > regularPrice) {
      priceWithDiscount = 1;
    }
    if (priceWithDiscount < 0) {
      priceWithDiscount = 0;
    }
    const discountPercent = priceWithDiscount / regularPrice * 100;
    $discountPercentField.val(discountPercent);
  });
});
/******/ })()
;
//# sourceMappingURL=admin.js.map