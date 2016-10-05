// autocomplet : this function will be executed every time we change the text
function autocomplet(csrf_token) {

    var min_length = 1; // min caracters to display the autocomplete
    var keyword = $('#show_only').val();
    var show_only_type = $('#show_only_type option:selected').val();

    if(show_only_type != "none"){
        if (keyword.length >= min_length) {
            $.ajax({
                url: '/cretin_app_analytics/server.php/autoCompleteAnalytics',
                type: 'get',
                data: {keyword:keyword,show_only_type:show_only_type,_token:csrf_token},
                success:function(data){
                    $('#list_id').show();
                    $('#list_id').html(data);
                }
            });
        } 
        else {
            $('#list_id').hide();
        }
    }
}

// autocomplete : this function will be executed every time we change the text
function autocomplet1(csrf_token) {

    var min_length = 1; // min caracters to display the autocomplete
    var keyword = $('#graph_filter_value').val();
    var show_only_type = $('#graph_filter_type option:selected').val();
    var graph=1;

    if(show_only_type != "none"){
        if (keyword.length >= min_length) {
            $.ajax({
                url: '/cretin_app_analytics/server.php/autoCompleteAnalytics',
                type: 'POST',
                data: {keyword:keyword,show_only_type:show_only_type,_token:csrf_token,graph:graph},
                success:function(data){
                    $('#filter_id').show();
                    $('#filter_id').html(data);
                }
            });
        } 
        else {
            $('#filter_id').hide();
        }
    }
}


// set_item : this function will be executed when we select an item
function set_item(item) {
    // change input value
    $('#show_only').val(item);
    // hide proposition list
    $('#list_id').hide();
}

// set_item : this function will be executed when we select an item
function set_item1(item) {
    // change input value
    $('#graph_filter_value').val(item);
// hide proposition list
    $('#filter_id').hide();
}
