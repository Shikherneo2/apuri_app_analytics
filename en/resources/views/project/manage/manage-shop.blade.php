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

    <form class="ink-form ink-formvalidator" id="employee" method="post" action="addShop">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Shop Name" data-rules="required" name="name" id="shop-name" />
            </div>
        </div>
        @if( !isset($user_type) || (isset($user_type) && $user_type != 4 ) )
         <div class="control-group required">
            <div class="control input_container ink-navigation">
                <input type="text" placeholder="Franchise Name"  onkeyup='autocomplet("{{csrf_token()}}")'  data-rules="required" name="franchise_id" id="franchise_id" autocomplete="off"   />
                <ul id="franchise_list_id"></ul>
            </div>
        </div>
        @endif
        <div class="control-group required">
            <div class="control">
                <textarea name="location" id="shop-location" data-rules="required" >Address
                </textarea>
            </div>
        </div>

        <input type="submit" name="Add" id="add" value="Add Shop" class="ink-button blue push-left" />

    </form>
    <br/>
    <form method="post" action="deleteShop" id="shop_table" class="ink-form ink-formvalidator" style="width:79%;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="ink-table" id="shop" style="margin-top:15px;">
            <thead>
            <tr>
                <th class="align-left">Shop ID</th>
                @if( !isset($user_type) || (isset($user_type) && $user_type != 4 ) )
                    <th class="align-left">Franchise Name</th>
                @endif
                <th class="align-left">Shop Name</th>
                <th class="align-left">Address</th>
                <th class="align-left" style="text-align:right;">All &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('Are you sure you want to delete this item?');" name="delete" id="delete" value="Delete" class="ink-button red push-right" />
    </form>

@endsection