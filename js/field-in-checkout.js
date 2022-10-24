jQuery(function () {

   // моё поле в чекауте
    let html = ' <div class="woocommerce-additional-fields__field-wrapper">' +
        '    <p class="form-row notes" id="field_in_checkout_metadata_p" data-priority="">' +
        '        <label for="field_in_checkout_metadata" class="">' +
        '            Кастомное поле' +
        '            <span class="optional">(_необязательно_)</span>' +
        '        </label>' +
        '        <span class="woocommerce-input-wrapper">' +
        '            <textarea name="field_in_checkout_metadata" class="input-text " id="field_in_checkout_metadata" placeholder="Введи сюда что-то важное!" rows="2" cols="5"></textarea>' +
        '        </span>' +
        '    </p>' +
        '</div>';

    let elPosts = jQuery('.form-row.notes');//нахожу базовое поле с комментарием

    elPosts.after(html);//добавляю под него своё
    jQuery('#field_in_checkout_metadata_p').css('border', '2px solid pink');

    let field_in_checkout_value =window.field_in_checkout.value;//значение
    let field_in_checkout_label =window.field_in_checkout.label;//подпись (так чтоб был перевод!)

    //если есть кастомное значение - тащу оттуда
    if (field_in_checkout_value !== '') {
    let html2 =    '<tr> <th id="custom">'+field_in_checkout_label+':</th> <td id="custom2">'+ field_in_checkout_value+'</td></tr>';

    let custom_tr = jQuery('.woocommerce-table.woocommerce-table--order-details.shop_table.order_details tr');
        custom_tr.last().after(html2);
    jQuery('#custom , #custom2').css('border', '2px dotted pink');
    }

});