@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
@endsection

@section('dataTablesLoader')
    $('#app').DataTable( {
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
            "ajax": "/cretin_app_analytics/server.php/view_app_records",
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                $("#app").find("tr").each(function(){
                    var value = $($(this).children("td")[0]).text();
                    var platform = $($(this).children("td")[2]).text();
                    if(platform==1){
                        $($(this).children("td")[2]).replaceWith("<td>iOS</td>");
                    }
                    else{
                        $($(this).children("td")[2]).replaceWith("<td>アンドロイド</td>");
                    }
                    $($(this).children("td")[3]).replaceWith("<td><a href ='/cretin_app_analytics/server.php/show-updateapp/"+value+"' id=Edit> 編集</a></td>");
                    $($(this).children("td")[4]).replaceWith("<td><input  value="+value+"  type=checkbox name=del_ids[] class=checkbox ></td>")}) ;

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

    <br/>
    <form method="post" action="deleteApp" class="ink-form ink-formvalidator">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="ink-table" id="app">
            <thead>
            <tr>
                <th class="align-left">アプリ ID</th>
                <th class="align-left">アプリ 名前</th>
                <th class="align-left">プラットフォーム</th>
                <th class="align-left">編集</th>
                <th class="align-left">すべて &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('この項目を削除してもよろしいですか？');" name="delete" id="delete" value="削除する" class="ink-button red push-right" />
    </form>

@endsection