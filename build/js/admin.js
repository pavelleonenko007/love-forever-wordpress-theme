/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/admin/DressSort.js":
/*!********************************!*\
  !*** ./src/admin/DressSort.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const initDressSorting = () => {
  (function ($) {
    'use strict';

    $(document).ready(function () {
      document.head.insertAdjacentHTML('beforeend', `<style>
          .ui-state-highlight {background-color: pink !important;} 
        </style>`);
      const $table = $('.wp-list-table');
      const $tbody = $table.find('tbody');
      const postsPerPage = parseInt($('#edit_dress_per_page').val()) || 10;
      $tbody.sortable({
        placeholder: 'ui-state-highlight',
        classes: {
          'ui-sortable': 'sortable',
          'ui-sortable-handle': 'sortable__handle',
          'ui-sortable-helper': 'sortable__helper'
        },
        handle: '.column-menu_order',
        axis: 'y',
        items: '> tr',
        helper: fixHelper,
        update: function (event, ui) {
          const order = [];
          $tbody.find('tr').each(function () {
            order.push($(this).attr('id').replace('post-', ''));
          });
          const page = parseInt(new URLSearchParams(window.location.search).get('paged')) || 1;
          const dressCategorySlug = new URLSearchParams(window.location.search).get('dress_category');
          $.ajax({
            url: LOVE_FOREVER_ADMIN.AJAX_URL,
            type: 'POST',
            data: {
              action: 'update_dress_order',
              order: order,
              page: page,
              posts_per_page: postsPerPage,
              dress_category: dressCategorySlug,
              nonce: LOVE_FOREVER_ADMIN.NONCE
            },
            success: function (response) {
              if (!response.success) {
                console.error(response.data.debug);
                alert(response.data.message);
                return;
              }

              // Обновляем отображаемые номера
              updateDisplayOrder(response.data.result);
            },
            error: function () {
              alert('Произошла ошибка при обновлении порядка.');
              window.location.reload();
            }
          });
        },
        start: function (e, ui) {
          const $targetElement = $(ui.item);
          const $placeholder = $(ui.placeholder);
          $placeholder.find('td').attr('colspan', $targetElement.find('th:not(.hidden),td:not(.hidden)').length);
        },
        stop: function (e, ui) {
          $tbody.css('width', '');
        }
      });
      $tbody.disableSelection();
      function updateDisplayOrder(orderData) {
        for (const index in orderData) {
          if (Object.prototype.hasOwnProperty.call(orderData, index)) {
            const postId = orderData[index];
            $(`#post-${postId} .column-menu_order`).text(index);
          }
        }
      }
    });
    function fixHelper(e, ui) {
      ui.children().each(function () {
        $(this).width($(this).width());
      });
      return ui;
    }
  })(jQuery);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (initDressSorting);

/***/ }),

/***/ "./src/admin/PercentControl.js":
/*!*************************************!*\
  !*** ./src/admin/PercentControl.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initPercentControl)
/* harmony export */ });
const REGULAR_PRICE_FIELD_SELECTOR = "#regularPriceField input";
const DISCOUNT_PERCENT_FIELD_SELECTOR = "#salePercentField input";
const PRICE_WITH_DISCOUNT_FIELD_SELECTOR = "#priceWithDiscountField input";
function initPercentControl() {
  (function ($) {
    "use strict";

    const $regularPriceField = $(REGULAR_PRICE_FIELD_SELECTOR);
    const $discountPercentField = $(DISCOUNT_PERCENT_FIELD_SELECTOR);
    const $priceWithDiscountField = $(PRICE_WITH_DISCOUNT_FIELD_SELECTOR);
    $discountPercentField.on("input", event => {
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
    $priceWithDiscountField.on("input", event => {
      let regularPrice = $regularPriceField.val() ? parseInt($regularPriceField.val()) : 0;
      let priceWithDiscount = $priceWithDiscountField.val() ? parseInt($priceWithDiscountField.val()) : 0;
      if (priceWithDiscount > regularPrice) {
        priceWithDiscount = regularPrice - 1;
      }
      if (priceWithDiscount < 0) {
        priceWithDiscount = 1;
      }
      const discountPercent = regularPrice > 0 && priceWithDiscount > 0 ? Math.round((regularPrice - priceWithDiscount) / regularPrice * 100) : 0;
      console.log({
        discountPercent
      });
      $discountPercentField.val(discountPercent);
    });
  })(jQuery);
}

/***/ }),

/***/ "./src/admin/styles/admin.scss":
/*!*************************************!*\
  !*** ./src/admin/styles/admin.scss ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!****************************!*\
  !*** ./src/admin/index.js ***!
  \****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _DressSort__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DressSort */ "./src/admin/DressSort.js");
/* harmony import */ var _PercentControl__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./PercentControl */ "./src/admin/PercentControl.js");
/* harmony import */ var _styles_admin_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styles/admin.scss */ "./src/admin/styles/admin.scss");



jQuery(document).ready(function ($) {
  (0,_DressSort__WEBPACK_IMPORTED_MODULE_0__["default"])();
  (0,_PercentControl__WEBPACK_IMPORTED_MODULE_1__["default"])();
});
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map