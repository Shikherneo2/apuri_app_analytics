// autocomplet : this function will be executed every time we change the text
function autocomplet(csrf_token) {

    var min_length = 1; // min caracters to display the autocomplete
    var keyword = $('#shop_id').val();
    if (keyword.length >= min_length) {
        $.ajax({
            url: '/cretin_app_analytics/server.php/autoShopCompleteShopkeeper',
            type: 'get',
            data: {keyword:keyword,_token:csrf_token},
            success:function(data){
                $('#shop_list_id').show();
                $('#shop_list_id').html(data);
            }
        });
    } else {
        $('#shop_list_id').hide();
    }
}

// set_item : this function will be executed when we select an item
function set_item(item) {
    // change input value
    $('#shop_id').val(item);
    // hide proposition list
    $('#shop_list_id').hide();
}