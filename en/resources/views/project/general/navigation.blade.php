<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cretin App Analytics</title>
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
</head>
<body class="news">

<nav class="ink-navigation" id="nav">
    <ul class="menu horizontal black" id="nav" >
        <span class="align-center push-center">
            <li> <a href="/cretin_app_analytics/server.php/home"><i class="fa fa-home fa-2x"></i><br/>Home</a></li>
            @if( !isset($type) || $type != 6 )
            <li> <a href="/cretin_app_analytics/server.php/analytics"><i class="fa fa-line-chart fa-2x"></i><br/>Install Analytics</a></li>
            @endif
            <li><a href="/cretin_app_analytics/server.php/graph_analytics"><i class="fa fa-bar-chart fa-2x"></i><br/>Graphs</a></li>
            <li> <a  href="/cretin_app_analytics/server.php/change_password"><i class="fa fa-unlock-alt fa-2x"></i><br/>Change Password</a></li>
            <li><a  href="/cretin_app_analytics/server.php/logout"><i class="fa fa-sign-out fa-2x"></i><br/>Logout</a></li>
        </span>
    </ul>
</nav>

@yield('content')

<footer>
    <!-- Styles -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel='stylesheet' type='text/css'>
    <link href="https://cdn.datatables.net/buttons/1.0.0/css/buttons.dataTables.min.css" rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-flex.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-legacy.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/font-awesome.css">
    <link href="/cretin_app_analytics/public/css/style.css" rel="stylesheet" />
    <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="/cretin_app_analytics/public/css/style_ie.css">
    <link rel="stylesheet" type="text/css" href="/cretin_app_analytics/public/css/ink-ie.css">
    <![endif]-->
    
    <!-- Scripts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ink/3.1.10/js/ink-all.min.js"></script>
    <!-- Autoload is required for the examples on this page. Read more below. -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ink/3.1.10/js/autoload.min.js" charset="utf-8"></script>
    
    <!-- scripts specific to specific pages -->
    @yield('specificScripts')
    
    <!--[if lt IE 10]>
    <script src="/cretin_app_analytics/public/js/placeholders.min.js"></script>
    <![endif]-->
    <script type="text/javascript">

        /*Clicking outside a suggestion box makes it disappear
        */
        $(document).ready(function () {
            $(document).click(function (event) {
                if (!$(event.target).closest('#franchise_list_id').length) {
                    if ($('#franchise_list_id').is(":visible")) {
                        $('#franchise_list_id').hide()
                    }
                }
                else if (!$(event.target).closest('#shop_list_id').length) {
                    if ($('#shop_list_id').is(":visible")) {
                        $('#shop_list_id').hide()
                    }
                }
                else if (!$(event.target).closest('#list_id').length) {
                    if ($('#list_id').is(":visible")) {
                        $('#list_id').hide()
                    }
                }
                else if (!$(event.target).closest('#filter_id').length) {
                    if ($('#filter_id').is(":visible")) {
                        $('#filter_id').hide()
                    }
                }
            })
        });
    </script>

    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="/cretin_app_analytics/public/js/jquery.iframe-transport.js"></script>
    <script>
    /*
     The DataTables handling
    */
    $(document).ready(function() {
        //Get the dataTablesLoader Code from the individual child templates 
        @yield('dataTablesLoader')
    });
    </script>
    
</footer>
</body>
</html>