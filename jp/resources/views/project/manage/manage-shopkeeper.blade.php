@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/autoshopcomplete.js"></script>
    <style>
        .checkbox_container{
            text-align: right;
            padding-right: 18px !important;
        }
    </style>
@endsection

@section('dataTablesLoader')
    $('#shopkeeper').DataTable( {
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
            "ajax": "/cretin_app_analytics/server.php/view_shopkeeper_records",
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                $("#shopkeeper").find("tr").each(function(){
                    var value = $($(this).children("td")[0]).text();
                    $($(this).children("td")[4]).replaceWith("<td class=checkbox_container><input  value="+value+"  type=checkbox name=del_ids[] class=checkbox ></td>")}) ;
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

    <form class="ink-form ink-formvalidator" id="employee" method="post" role="form" action="addShopkeeper" style="width:79%;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="店主の名前" data-rules="required|text[true,false]" name="name" id="name" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="メール　アドレス" name="email" id="e-mail" data-rules="required|email" />
            </div>
        </div>
        <div class="control-group required ">
            <div class="control  input_container ink-navigation">
                <input type="text" placeholder="ショップの名前" name="shop_id" id="shop_id" data-rules="required" onkeyup='autocomplet("{{csrf_token()}}")' autocomplete="off" />
                <ul id="shop_list_id"></ul>
            </div>
        </div>
        <input type="submit" name="Add" id="add" value="店主を加える" class="ink-button blue push-left" />

    </form>
    <br/>

    <form class="ink-form ink-formvalidator" method="post" role="form" action="deleteShopkeeper">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <table class="ink-table" id="shopkeeper" style="margin-top:15px;">
            <thead>
            <tr>
                <th class="align-left">店主のID</th>
                <th class="align-left">名前</th>
                <th class="align-left">ショップのID</th>
                <th class="align-left">ショップの名前</th>
                <th class="align-left" style="text-align:right;">すべて &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('この項目を削除してもよろしいですか？');" name="delete" id="delete" value="削除をする" class="ink-button red push-right" />
    </form>

@endsection