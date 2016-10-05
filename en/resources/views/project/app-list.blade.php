@extends('project.general.navigation')

@section('specificScripts')
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
@endsection

@section('dataTablesLoader')
    $('#app').DataTable( {
            dom: 'lfrtip',
            "language": {
                "sEmptyTable":     "No data in the table",
                "sInfo":           " Showing from _START_ to _END_ from total _TOTAL_ matching records",
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
                        $($(this).children("td")[2]).replaceWith("<td>Android</td>");
                    }
                    $($(this).children("td")[3]).replaceWith("<td><a href ='/cretin_app_analytics/server.php/show-updateapp/"+value+"' id=Edit>Edit</a></td>");
                    $($(this).children("td")[4]).replaceWith("<td><input  value="+value+" type=checkbox name=del_ids[] class=checkbox ></td>")}) ;

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

    <br/>
    <form method="post" action="deleteApp" class="ink-form ink-formvalidator">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="ink-table" id="app">
            <thead>
            <tr>
                <th class="align-left">App ID</th>
                <th class="align-left">App Name</th>
                <th class="align-left">Platform</th>
                <th class="align-left">Edit</th>
                <th class="align-left">All &nbsp; &nbsp;<input id="check-all" name="check-all" value="ck" type="checkbox"></th>
            </tr>
            </thead>
        </table>
        <input type="submit" onclick="return confirm('Are you sure you want to delete this item？');" name="delete" id="delete" value="Delete" class="ink-button red push-right" />
    </form>

@endsection