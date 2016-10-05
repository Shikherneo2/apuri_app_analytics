@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
@endsection

@section('dataTablesLoader')
    $('#admin').DataTable( {
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
            "ajax": "/cretin_app_analytics/server.php/view_admin_records",
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                $("#admin").find("tr").each(function(){
                    var value = $($(this).children("td")[0]).text();
                    $($(this).children("td")[3]).replaceWith("<td><input  value="+value+"  type=checkbox name=del_ids[] class=checkbox ></td>")}) ;
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
    
    <form class="ink-form ink-formvalidator" role="form" id="employee" method="POST" action="addAdmin">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="管理者の名前" data-rules="required|text[true,false]" name="name" id="admin_name" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="管理者のID" name="userid" id="adminID" data-rules="required|text[true,false]" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="メール　アドレス" name="email" id="e-mail" data-rules="required|email" />
                <i class="fa fa-envelope-o"></i>
            </div>
        </div>
        <input type="submit" name="add_admin" id="add_admin" value="管理者を加える" class="ink-button blue push-left" />

    </form>
    <br/>
  <form action="deleteAdmin" method="post" class="ink-form ink-formvalidator">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <span id="search-admin" class="push-center">
        
        </span>
        <table class="ink-table" id="admin">
            <ul>
                <thead>
                <tr>
                    <th class="align-left">ユーザＩＤ</th>
                    <th class="align-left">名前</th>
                    <th class="align-left">メールアドレス</th>
                    <th class="align-left">すべて　&nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
                </tr>
                </thead>
                <tbody>
                
                </tbody>
            </ul>
        </table>
        <input type="submit" onclick="return confirm('この項目を削除してもよろしいですか？');" name="delete" id="delete" value="削除する" class="ink-button red push-right" />
    </form>
   
@endsection