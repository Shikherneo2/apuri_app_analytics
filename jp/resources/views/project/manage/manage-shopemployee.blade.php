@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/autoshopcomplete.js"></script>
    <script src="/cretin_app_analytics/public/js/SimpleAjaxUploader.js"></script>
    <script>
        function escapeTags( str ) {
            return String( str )
                    .replace( /&/g, '&amp;' )
                    .replace( /"/g, '&quot;' )
                    .replace( /'/g, '&#39;' )
                    .replace( /</g, '&lt;' )
                    .replace( />/g, '&gt;' );
        }

        $(document).ready(function() {

            var btn = document.getElementById('uploadBtn');

            var uploader = new ss.SimpleUpload({
                button: btn,
                url: '/cretin_app_analytics/server.php/upload_csv',
                name: 'csv',
                multipart: true,
                hoverClass: 'hover',
                focusClass: 'focus',
                customHeaders: { 
                    'X-CSRF-TOKEN': $("#_token").val()
                },
                responseType: 'json',
                allowedExtensions: ["csv"],
                onSubmit: function() {
                    $("#uploadBtn").val("アップロードをしてる.....");
                    $("#msgBox").text("");
                    $("#msgBox").prepend('<img src="/cretin_app_analytics/public/images/loader.gif" id="loader_img" height="30">');    
                },
                onComplete: function( filename, response ) {
                    $("#uploadBtn").val("CSVを変更する");
                    $("#loader_img").remove();

                    if ( !response ) {
                        msgBox.innerHTML = 'おっとっと! アップロードできない。';
                        return;
                    }
                    else{                
                        if ( response.error != "1" ){ 
                            msgBox.innerHTML = '<strong>' + escapeTags( filename ) + '</strong>' + ' をアップロードしました。';
                            $("#uploaded_image").remove();
                            if( $("#add_users").length == 0 ){
                                $("#msgBox").append('<div id="uploaded_csv_box"><a href="/cretin_app_analytics/server.php/addShop_employee_from_csv"><input type=button class="ink-button blue" id=add_users value="CSVからユーザを加える"></a></div>');
                            }
                        }
                        else   
                            msgBox.innerHTML = 'おっとっと! アップロードできない。';
                    }
                },
                onError: function() {
                    msgBox.innerHTML = 'おっとっと! アップロードできない。';
                }
            });
        });
    </script>
    <style>
        .checkbox_container{
            text-align: right;
            padding-right: 18px !important;
        }
    </style>
@endsection

@section('dataTablesLoader')
    $('#shop-employee').DataTable( {
            dom: 'lfrtip',
            "language": {
                "sEmptyTable":     "テーブルにデータがありません",
                "sInfo":           " _TOTAL_ 件中 _START_ から _END_ まで表示",
                "sInfoEmpty":      " 0 件中 0 から 0 まで表示",
                "sInfoFiltered":   "（全 _MAX_ 件より抽出）",
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     "_MENU_ 件表示",
                "sLoadingRecords": "読み込み中...",
                "sProcessing":     "処理中...",
                "sSearch":         "検索:",
                "sZeroRecords":    "一致するレコードがありません",
                "oPaginate": {
                        "sFirst":    "先頭",
                        "sLast":     "最終",
                        "sNext":     "次",
                        "sPrevious": "前"
                    },
                "oAria": {
                      "sSortAscending":  ": 列を昇順に並べ替えるにはアクティブにする",
                      "sSortDescending": ": 列を降順に並べ替えるにはアクティブにする"
                }
            },
            "processing": true,
            "serverSide": true,
            "ajax": "/cretin_app_analytics/server.php/view_shop_employee_records",
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                $("#shop-employee").find("tr").each(function(){
                    var value = $($(this).children("td")[0]).text();
                    last_no = ($(this).children("td")).length;
                    if(last_no >1)
                        $($(this).children("td")[last_no-1]).replaceWith("<td class=checkbox_container><input  value="+value+" type=checkbox name=del_ids[] class=checkbox ></td>")}) ;
                $("#check-all").click(function () {
                    $(".checkbox").prop('checked', $(this).prop('checked'));
                });
            }
    });
@endsection

@section('content')

    @if (count($errors) > 0)
        <div class="align-center" style="margin-top:10px; color:#B71C1C;">
            <strong>おっとっと!</strong> リクエスト処理中に問題が発生しました.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (Session::get('success'))
        <div class="align-center" style="margin-top:10px; color:#004D40;">
            {{ Session::get('success') }} 
        </div>
    @endif
    <form class="ink-form ink-formvalidator" id="employee" method="post" role="form" action="addShop_employee" >
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="_token">
        <div class="control-group required">
            <div class="control">
                <input placeholder="名前" type="text" data-rules="required|text[true,false]" name="name" id="name" />
            </div>
        </div>
        
        @if( !isset($user_type) || (isset($user_type) && $user_type != 5 ) )
        <div class="control-group required">
            <div class="control input_container ink-navigation">
                <input placeholder="ショップの名前" type="text" name="shop_id" onkeyup='autocomplet("{{csrf_token()}}")' id="shop_id" data-rules="required|text[true,false]" autocomplete="off"/>
                <ul id="shop_list_id"></ul>
            </div>
        </div>
        @endif
        
        <div class="control-group required">
          <div class="control">
                <input placeholder="メール　アドレス" type="text" name="email" id="e-mail" data-rules="required|email" />
            </div>

        <div class="button-group horizontal-gutters" style="margin-top:15px;">
        <input type="submit" name="Add" id="add" value="店員を加える" class="ink-button blue push-left gutters" />
        <input type="button" class="ink-button grey push-center gutters" value="新しいCSVをアップロードする" id="uploadBtn" style="width:300px;" accept="csv/*">
        </div>
                <div id="msgBox" style="display:block;margin-top:5px;margin-bottom:5px;"></div>
                @if( !empty($uploaded_csv) )
                    <div id="uploaded_csv_box" class="control-group gutters" style="margin-top:10px;margin-bottom:15px;">
                        <a href="/cretin_app_analytics/server.php/addShop_employee_from_csv" style="display:table-cell;">
                            <input type=button id="add_users" class="ink-button green" value="CSVからユーザを加える"></a>
                        <p style="display:table-cell;padding-left:10px;vertical-align:middle;">( 前のアップロドしたCSVファイルもあります。)</p> 
                    </div>
                @endif

                <a class="active" href="/cretin_app_analytics/public/uploads/example.csv">サンプルファイルをダウンロードする</a>
        </div>
   </form>
    <br/>
    <form action="/cretin_app_analytics/server.php/deleteShop_employee" method="post" class="ink-form ink-formvalidator">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="ink-table" id="shop-employee">
            <thead>
            <tr>
                <th class="align-left">店員のID</th>
                <th class="align-left">名前</th>
                @if( !isset($user_type) || (isset($user_type) && $user_type != 5 ) )
                    <th class="align-left">ショップのID</th>
                    <th class="align-left">ショップの名前</th>
                @endif
                <th class="align-left" style="text-align:right;">すべて&nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('この項目を削除してもよろしいですか？');" name="delete" id="delete" value="削除をする" class="ink-button red push-right" />
    </form>

@endsection