(()=>{"use strict";const e=()=>{!function(e){function t(t,n){return n.children().each((function(){e(this).width(e(this).width())})),n}e(document).ready((function(){const n=e(".wp-list-table").find("tbody"),a=parseInt(e("#edit_dress_per_page").val())||10;n.sortable({classes:{"ui-sortable":"sortable","ui-sortable-handle":"sortable__handle","ui-sortable-helper":"sortable__helper"},handle:".column-menu_order",axis:"y",helper:t,update:function(t,r){const o=[];n.find("tr").each((function(){o.push(e(this).attr("id").replace("post-",""))}));const s=parseInt(new URLSearchParams(window.location.search).get("paged"))||1,c=new URLSearchParams(window.location.search).get("dress_category");e.ajax({url:LOVE_FOREVER_ADMIN.AJAX_URL,type:"POST",data:{action:"update_dress_order",order:o,page:s,posts_per_page:a,dress_category:c,nonce:LOVE_FOREVER_ADMIN.NONCE},success:function(t){if(!t.success)return console.error(t.data.debug),void alert(t.data.message);!function(t){for(const n in t)if(Object.prototype.hasOwnProperty.call(t,n)){const a=t[n];e(`#post-${a} .column-menu_order`).text(n)}}(t.data.result)},error:function(){alert("Произошла ошибка при обновлении порядка."),window.location.reload()}})}})}))}(jQuery)};jQuery(document).ready((function(t){e(),function(e){const t=e("#regularPriceField input"),n=e("#salePercentField input"),a=e("#priceWithDiscountField input");n.on("input",(e=>{const r=t.val()?parseInt(t.val()):0,o=n.val()?parseInt(n.val()):0;o>99&&n.val(99),o<0&&n.val(0);const s=r-r*o/100;a.val(Math.ceil(s))})),a.on("input",(e=>{let r=t.val()?parseInt(t.val()):0,o=a.val()?parseInt(a.val()):0;o>r&&(o=r-1),o<0&&(o=1);const s=r>0&&o>0?Math.round((r-o)/r*100):0;console.log({discountPercent:s}),n.val(s)}))}(jQuery)}))})();