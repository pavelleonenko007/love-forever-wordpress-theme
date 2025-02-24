const initDressSorting = () => {
  (function ($) {
    "use strict";

    $(document).ready(function () {
      const $table = $(".wp-list-table");
      const $tbody = $table.find("tbody");
      const postsPerPage = parseInt($("#edit_dress_per_page").val()) || 10;

      $tbody.sortable({
        classes: {
          "ui-sortable": "sortable",
          "ui-sortable-handle": "sortable__handle",
          "ui-sortable-helper": "sortable__helper",
        },
        handle: ".column-menu_order",
        axis: "y",
        helper: fixHelper,
        update: function (event, ui) {
          const order = [];
          $tbody.find("tr").each(function () {
            order.push($(this).attr("id").replace("post-", ""));
          });

          const page =
            parseInt(
              new URLSearchParams(window.location.search).get("paged")
            ) || 1;
          const dressCategorySlug = new URLSearchParams(
            window.location.search
          ).get("dress_category");

          $.ajax({
            url: LOVE_FOREVER_ADMIN.AJAX_URL,
            type: "POST",
            data: {
              action: "update_dress_order",
              order: order,
              page: page,
              posts_per_page: postsPerPage,
              dress_category: dressCategorySlug,
              nonce: LOVE_FOREVER_ADMIN.NONCE,
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
              alert("Произошла ошибка при обновлении порядка.");
              window.location.reload();
            },
          });
        },
      });

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

export default initDressSorting;
