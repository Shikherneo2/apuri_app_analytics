@extends('project.general.navigation')
    
@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/AnalyticsAutoComplete.js"></script>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/AnalyticsAutoComplete2.js"></script>
    <style>
    #analytics_length{
        margin-bottom: 15px;
    }
    </style>
@endsection

@section('dataTablesLoader')
    dataTableObject = null;
    state = true;
    current = "all";
    japMap = { "franchise":"フランチャイズ", "shop":"ショップ", "employee": "店員", "app":"アプリ"};
    all_options = [
        '<option value="franchise" id="franc_filter">フランチャイズ</option>',
        '<option value="shop" id="shop_filter">ショップ</option>',
        '<option value="employee" id="emp_filter">店員</option>'
    ];
    $("#do_filter").click(function(){
            dataTableObject.ajax.reload();
        });

        $("#device_type, #group_by, #show_only_type").change(function(){
            this_object = $(this);
            
            if( $(this_object).attr("id")=="group_by"){
                if( $(this_object).val()=="franchise" ){
                    if(current == "all" || current == "app"){
                        $("#shop_filter").remove();
                        $("#emp_filter").remove();
                    }
                    else if(current == "shop"){
                        $("#shop_filter").remove();
                    }
                    else if(current == "employee"){
                        $("#shop_filter").remove();
                    }
                }
                else if( $(this_object).val()=="shop" ){
                    if(current == "all" || current == "app"){
                        $("#emp_filter").remove();
                    }
                    else if(current == "franchise"){
                        $("#show_only_type").append(all_options[1]);
                    }
                    else if(current == "employee"){
                        $("#emp_filter").remove();
                    }
                }
                else if( $(this_object).val()=="all" || $(this_object).val()=="employee" || $(this_object).val()=="app" ){
                   if(current == "franchise"){
                        $("#show_only_type").append(all_options[1]);
                        $("#show_only_type").append(all_options[2]);
                    }
                    else if(current == "shop"){
                        $("#show_only_type").append(all_options[2]);
                    }
                }

                current = $(this_object).val();
            }

            if( $(this).attr("id")=="group_by" && $(this).val() != "all"){
                state = false;
                temp = $(this_object).val();
                if( $("#analytics").find("th").length ==4 ){
                    $("#column_third").remove();
                    $("#column_fourth").remove();
                    $("#column_second").text("インストル");
                }
                $("#column_first").text( japMap[temp] );
            }
            else if( $(this).attr("id") == "group_by" && $(this).val() == "all" ){
                state = true;
                if( $("#analytics").find("th").length !=4 ){
                    $("#analytics").find("thead").children("tr").append('<th class="align-left" id="column_third">ショップ</th>');
                    $("#analytics").find("thead").children("tr").append('<th class="align-left" id="column_fourth">タイムスタンプ</th>');
                    $("#column_first").text("アプリ");
                    $("#column_second").text("店員");
                }
            }
            if( $(this).attr("id")!="show_only_type" )
                dataTableObject.ajax.reload();
        });

    dataTableObject = $('#analytics').DataTable({
            dom: 'lrtip',
            "bPaginate":true,
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
            "ajax": {
                "url":"/cretin_app_analytics/server.php/get_analytics",
                "data": function(d){
                    d.device_type = $("#device_type").val();
                    d.group_by = $("#group_by").val();
                    d.show_only_type = $("#show_only_type").val();
                    d.show_only = $("#show_only").val();
                }
            },
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                if(state === false){
                    $("#analytics").children("tbody").children("tr").each(function(){
                        $( $(this).children("td")[3] ).remove();
                        $( $(this).children("td")[2] ).remove();
                    });
                }
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

<br>
<br>
<div class="ink-form push-center control-group"id="employee">
    <span class="control">グループ
        <select name="group_by" id="group_by" class="control" style="height:42px;">
            <option value="all">すべて</option>
            @if ($type <= 3)
                <option value="franchise">フランチャイズ</option>
            @endif
            @if ($type <= 4)
                <option value="shop">ショップ</option>
            @endif
            <option value="app">アプリ</option>
            @if ($type <= 5)
                <option value="employee">店員</option>
            @endif
        </select>
    </span>
    <span id="platform" class="control"> プラットフォーム
        <select name="device_type" id="device_type" class="control" style="height:42px;">
            <option value="all">すべて</option>
            <option value="ios">iOS</option>
            <option value="android">アンドロイド</option>
        </select>
    </span>
    <span id="filter_by" class="control ink-navigation">フィルタ
        <select name="show_only_type" id="show_only_type" class="control" style="height:42px;">
            @if ($type <= 3)
                <option value="franchise" id="franc_filter">フランチャイズ</option>
            @endif
            @if ($type <= 4)
                <option value="shop" id="shop_filter">ショップ</option>
            @endif
            <option value="app">アプリ</option>
            @if ($type <= 5)
            <option value="employee" id="emp_filter">店員</option>
            @endif
        </select>
        <input type="text" name="show_only" id="show_only" class="control" onkeyup='autocomplet("{{csrf_token()}}")' autocomplete="off" >
        <ul id="list_id"></ul>
        <input type="button" class="ink-button red push-right" value="フィルタ" id="do_filter" style="height: 38px;margin-top: 2px;margin-left: 5px;">
    </span>

</div>

<table class="ink-table" id="analytics" style="margin-top:15px;">
    <thead>
    <tr>
        <th class="align-left" id="column_first">アプリ</th>
        <th class="align-left" id="column_second">店員</th>
        <th class="align-left" id="column_third">ショップ</th>
        <th class="align-left" id="column_fourth">タイムスタンプ</th>
    </tr>
    </thead>
</table>
<br>

@endsection