@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <style>
        .checkbox_container{
            text-align: right;
            padding-right: 18px !important;
        }
    </style>
@endsection

@section('dataTablesLoader')
    $('#franchise').DataTable({
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
            "ajax": "/cretin_app_analytics/server.php/view_franchise_records",
            "bSort":false,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "fnDrawCallback": function(oSettings, json) {
                $("#franchise").find("tr").each(function(){
                    var value = $($(this).children("td")[0]).text();
                    $($(this).children("td")[3]).replaceWith("<td class=checkbox_container><input  value="+value+"  type=checkbox name=del_ids[] class=checkbox ></td>")}) ;
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

    <form class="ink-form ink-formvalidator" id="employee" method="post" role="form" action="addFranchise">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Franchise Name" data-rules="required" name="name" id="franchise_name" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" name="email" placeholder="Email" id="email" data-rules="required|email" />
                <i class="fa fa-envelope-o"></i>
            </div>
        </div>

        <input type="submit" name="add_franchise" id="add_franchise" value="Add Franchise" class="ink-button blue push-left" />

    </form>
    <br/>
    <form action="deleteFranchise" method="post" class="ink-form ink-formvalidator">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <table class="ink-table" id="franchise" style="margin-top:15px;">
            <thead>
            <tr>
                <th class="align-left">User ID</th>
                <th class="align-left">Name</th>
                <th class="align-left">Email</th>
                <th class="align-left" style="text-align:right;">All &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('Are your sure you want to delete this item?');" name="delete" id="delete" value="Delete" class="ink-button red push-right" />
    </form>

@endsection