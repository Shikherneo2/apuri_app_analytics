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
                "sEmptyTable":     "No data in the table",
                "sInfo":           " Showing _START_ to _END_ from total _TOTAL_ matching records",
                "sInfoEmpty":      " No records",
                "sInfoFiltered":   "（Filtered from _MAX_ total records）",
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     "_MENU_ / page",
                "sLoadingRecords": "Loading...",
                "sProcessing":     "Processing...",
                "sSearch":         "Search:",
                "sZeroRecords":    "No matching records found",
                "oPaginate": {
                    "sFirst":    "First",
                    "sLast":     "Last",
                    "sNext":     "Next",
                    "sPrevious": "Prev"
                },
                "oAria": {
                    "sSortAscending":  ": Sort in Ascending order",
                    "sSortDescending": ": Sort in Descending order"
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
            <strong>Sorry!</strong> There was an error processing your request.<br><br>            
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
                <input type="text" placeholder="Shopkeeper Name" data-rules="required|text[true,false]" name="name" id="name" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Email" name="email" id="e-mail" data-rules="required|email" />
            </div>
        </div>
        <div class="control-group required ">
            <div class="control  input_container ink-navigation">
                <input type="text" placeholder="Shop Name" name="shop_id" id="shop_id" data-rules="required" onkeyup='autocomplet("{{csrf_token()}}")' autocomplete="off" />
                <ul id="shop_list_id"></ul>
            </div>
        </div>
        <input type="submit" name="Add" id="add" value="Add Shopkeeper" class="ink-button blue push-left" />

    </form>
    <br/>

    <form class="ink-form ink-formvalidator" method="post" role="form" action="deleteShopkeeper">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <table class="ink-table" id="shopkeeper" style="margin-top:15px;">
            <thead>
            <tr>
                <th class="align-left">Shopkeeper ID</th>
                <th class="align-left">Name</th>
                <th class="align-left">Shop ID</th>
                <th class="align-left">Shop Name</th>
                <th class="align-left" style="text-align:right;">All &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('Are you sure you want to delete this item?');" name="delete" id="delete" value="Delete" class="ink-button red push-right" />
    </form>

@endsection