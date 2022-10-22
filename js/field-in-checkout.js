jQuery(function () {
   // моё поле в чекауте
    let html = ' <div class="woocommerce-additional-fields__field-wrapper">\n' +
        '    <p class="form-row notes" id="order_comments_field2" data-priority="">\n' +
        '        <label for="order_comments2" class="">\n' +
        '            Примечание к заказу&nbsp;\n' +
        '            <span class="optional">(необязательно)</span>\n' +
        '        </label>\n' +
        '        <span class="woocommerce-input-wrapper">\n' +
        '            <textarea name="order_comments2" class="input-text " id="order_comments2" placeholder="Примечания к вашему заказу, например, особые пожелания отделу доставки." rows="2" cols="5"></textarea>\n' +
        '        </span>\n' +
        '    </p>\n' +
        '</div>';
    let elPosts = jQuery('.form-row.notes');//нахожу базовое поле с комментарием
elPosts.after(html);//добавляю под него своё
    jQuery('#order_comments_field2').css('border', '5px solid red');

    //инфа из моего поля в финальном чекауте
   // class="woocommerce-table woocommerce-table--order-details shop_table order_details"
    let html2 =    '<tr> <th id="custom">днище:</th> <td id="custom2">qa</td> </tr>';

  //  let html2 ='<p>SSSS</p>';
let x = jQuery('.woocommerce-table.woocommerce-table--order-details.shop_table.order_details tr');
    x.last().after(html2);

    //jQuery('.woocommerce-table.woocommerce-table--order-details.shop_table.order_details tr th').css('border', '5px solid red');


    jQuery('#custom , #custom2').css('border', '2px dotted blue');

});





