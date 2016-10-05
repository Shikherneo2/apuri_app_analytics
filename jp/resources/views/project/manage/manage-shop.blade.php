@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <script src="/cretin_app_analytics/public/js/autocomplete.js"></script>
    <style>
        .checkbox_container{
            text-align: right;
            padding-right: 18px !important;
        }
    </style>
@endsection

@section('dataTablesLoader')
    
    $('#shop').DataTable( {
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
        "ajax": "/cretin_app_analytics/server.php/view_shop_records",
        "bSort":false,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "fnDrawCallback": function(oSettings, json) {
            $("#shop").find("tr").each(function(){
                var value = $($(this).children("td")[0]).text();
                last_no = ($(this).children("td")).length;
                $($(this).children("td")[last_no-1]).replaceWith('<td class=checkbox_container><input  value='+value+'  type=checkbox name=del_ids[] class=checkbox ></td>')}) ;
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

    <form class="ink-form ink-formvalidator" id="employee" method="post" action="addShop">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="ショップの名前" data-rules="required" name="name" id="shop-name" />
            </div>
        </div>
        @if( !isset($user_type) || (isset($user_type) && $user_type != 4 ) )
         <div class="control-group required">
            <div class="control input_container ink-navigation">
                <input type="text" placeholder="フランチャイズの名前"  onkeyup='autocomplet("{{csrf_token()}}")'  data-rules="required" name="franchise_id" id="franchise_id" autocomplete="off"   />
                <ul id="franchise_list_id"></ul>
            </div>
        </div>
        @endif
        <div class="control-group required">
            <div class="control">
                <textarea name="location" id="shop-location" data-rules="required" >住所
                </textarea>
            </div>
        </div>

        <input type="submit" name="Add" id="add" value="ショップを加える" class="ink-button blue push-left" />

    </form>
    <br/>
    <form method="post" action="deleteShop" id="shop_table" class="ink-form ink-formvalidator" style="width:79%;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="ink-table" id="shop" style="margin-top:15px;">
            <thead>
            <tr>
                <th class="align-left">ショップ ID</th>
                @if( !isset($user_type) || (isset($user_type) && $user_type != 4 ) )
                    <th class="align-left">フランチャイズの名前</th>
                @endif
                <th class="align-left">ショップの名前</th>
                <th class="align-left">住所</th>
                <th class="align-left" style="text-align:right;">すべて &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('この項目を削除してもよろしいですか？');" name="delete" id="delete" value="削除する" class="ink-button red push-right" />
    </form>

@endsection