@extends('project.general.navigation')

@section('specificScripts')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <style>
        #graph_group_by{
            height:40px;
        }
        #graph_filter_type{
            height:40px;
        }
        #start{
            height:35px;
        }
        #end{
            height:35px;
        }
        #range{
            height: 17px;
            width: 17px;
            vertical-align: middle;
            margin-left: 7px;
            cursor:pointer;
        }
    </style>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/AnalyticsAutoComplete.js"></script>
    <script type="text/javascript" src="/cretin_app_analytics/public/js/AnalyticsAutoComplete2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script type="text/javascript"
            src="https://www.google.com/jsapi?autoload={
                    'modules':[{
                      'name':'visualization',
                      'version':'1',
                      'packages':['corechart']
                    }]
                  }">
    </script>

    <script type="text/javascript">
        var params = "d", filter_by_value = "",filter_by="none";
        $(document).ready(function(){
            $("#draw").click(function(){
                params = $("#graph_group_by").val()[0];
                filter_by = $("#graph_filter_type").val();
                filter_by_value = $("#graph_filter_value").val();
                drawChart();
            });
        });

        google.setOnLoadCallback(drawChart);

        function drawChart() {
            var json = $.ajax({
                url: "/cretin_app_analytics/server.php/getDataForGraphs",
                data: { "group_by": params, 
                        "graph_filter_type" : filter_by, 
                        "graph_filter_value": filter_by_value,
                        "range": ($("#range").prop("checked") ? "1":"0"), 
                        "start": $("#start").val(), 
                        "end": $("#end").val() 
                    },
                async: false,
                dataType: json
            }).responseText;
            if( json.hasOwnProperty("error") ){
                console.log(json.error);
            }
            else{
                var data = new google.visualization.DataTable(json);
                var selected= document.getElementById("graph_group_by").selectedIndex;

                if (selected ==0)
                    var headtitle= 'Daily Installs';
                else if(selected ==1)
                     headtitle='Monthly Installs';
                else if(selected ==2)
                     headtitle='Weekly Installs';
                else if(selected == 3)
                    headtitle='Yearly Installs';

                var options = {
                    title: headtitle,
                    curveType: 'line',
                    legend: null,
                    hAxis: {
                            title: 'Time'
                            },
                    vAxis: {
                        title: 'Installs'
                    }
                };
                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                chart.draw(data, options);
                $("#download_img").remove();
                $("#png").append('<a id="download_img" class="ink-button blue" href="' + chart.getImageURI() + '" target="_blank">Download Graph</a>');
            }
        }
    </script>

    <script>
        $(function() {

            $( "#start" ).datepicker({
                closeText: "Close",
                prevText: "&#x3C;Previous",
                nextText: "Next&#x3E;",
                currentText: "Today",
                monthNames: [ "Jan","Feb","Mar","Apr","May","Jun",
                    "Jul","Aug","Sep","Oct","Nov","Dec" ],
                monthNamesShort: [ "Jan","Feb","Mar","Apr","May","Jun",
                    "Jul","Aug","Sep","Oct","Nov","Dec" ],
                dayNames: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                dayNamesShort: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                dayNamesMin: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                weekHeader: "Week",
                dateFormat: "yy/mm/dd",
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: "" ,
                setDate: "-1m",
                showDate: "-1m",
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $( "#end" ).datepicker( "option", "minDate", selectedDate );
                }

            });
            $( "#end" ).datepicker({
                closeText: "Close",
                prevText: "&#x3C;Previous",
                nextText: "Next&#x3E;",
                currentText: "Today",
                monthNames: [ "Jan","Feb","Mar","Apr","May","Jun",
                    "Jul","Aug","Sep","Oct","Nov","Dec" ],
                monthNamesShort: [ "Jan","Feb","Mar","Apr","May","Jun",
                    "Jul","Aug","Sep","Oct","Nov","Dec" ],
                dayNames: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                dayNamesShort: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                dayNamesMin: [ "Mon","Tue","Wed","Thurs","Fri","Sat","Sun" ],
                weekHeader: "Week",
                dateFormat: "yy/mm/dd",
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: "" ,
                setDate: "+0d",
                showDate:"+0d",
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $( "#start" ).datepicker( "option", "maxDate", selectedDate );
                }
            });
        });
    </script>
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

<br>
<br>

<center>
    <div class="ink-form control-group">
        <span class="control">Group　By:　
            <select name="graph_group_by" id="graph_group_by" class="control">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
            </select>
        </span>
        &nbsp; &nbsp;

        <span class="control ink-navigation">Filter 
            <select name="graph_filter_type" id="graph_filter_type" class="control">
                <option value="none">None</option>
                <option value="app">App</option>
                @if( $type <= 3 )
                    <option value="franchise">Franchise</option>
                @endif

                @if( $type <= 4 )
                    <option value="shop">Shop</option>
                @endif

                @if( $type <= 5 )
                    <option value="employee">Employee</option>
                @endif
                <br><br>
            </select> &nbsp; &nbsp;
            <input type="text" id="graph_filter_value" name="graph_filter_value" class="control" onkeyup='autocomplet1("{{csrf_token()}}")' autocomplete="off">
            <ul id="filter_id"></ul>
        </span>
        <br>
        <br>
        <label for="range">Select Range</label>
        <span class="control">
            <input type="checkbox" name="range" id="range">&nbsp; &nbsp; &nbsp;
          
            <input type="text" id="start" name="from" value='{{ $dates["start"] }}'>
            <label for="from">From</label>
            <input type="text" id="end" name="to" value='{{ $dates["end"] }}'>
            <label for="to">To</label>
        </span>
        <br><br>
    </div>

    <div class="button-group push-center" style="display: table !important;">
        <input type="button" class="ink-button blue" value="Draw Graph" id="draw">

        <div id='png' style="margin-top:5px;display:inline-block;"></div>
    </div>
    <br>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
    <br>
    <br>
</center>
@endsection