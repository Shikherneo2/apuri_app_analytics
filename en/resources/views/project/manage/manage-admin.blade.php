@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
@endsection

@section('dataTablesLoader')
    $('#admin').DataTable( {
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
    
    <form class="ink-form ink-formvalidator" role="form" id="employee" method="POST" action="addAdmin">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Admin Name" data-rules="required|text[true,false]" name="name" id="admin_name" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Admin ID" name="userid" id="adminID" data-rules="required|text[true,false]" />
            </div>
        </div>
        <div class="control-group required">
            <div class="control">
                <input type="text" placeholder="Email" name="email" id="e-mail" data-rules="required|email" />
                <i class="fa fa-envelope-o"></i>
            </div>
        </div>
        <input type="submit" name="add_admin" id="add_admin" value="Add Admin" class="ink-button blue push-left" />

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
                    <th class="align-left">User ID</th>
                    <th class="align-left">Name</th>
                    <th class="align-left">Email</th>
                    <th class="align-left">All　&nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
                </tr>
                </thead>
                <tbody>
                
                </tbody>
            </ul>
        </table>
        <input type="submit" onclick="return confirm('Are you sure you want to delete this item?');" name="delete" id="delete" value="Delete" class="ink-button red push-right" />
    </form>
   
@endsection