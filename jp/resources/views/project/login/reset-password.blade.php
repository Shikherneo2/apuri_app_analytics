<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>

    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>   
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-ie.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-flex.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-legacy.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/font-awesome.css">

    <link href="/cretin_app_analytics/public/css/style.css" rel="stylesheet" />
    <!--[if lt IE 9]><link rel="stylesheet" href="http://172.16.15.169/public/css/style_ie.css"><![endif]-->

    <!-- Load the js files -->
    <script type="text/javascript" src="http://fastly.ink.sapo.pt/3.1.10/js/ink-all.js"></script>
    <!-- Autoload is required for the examples on this page. Read more below. -->
    <script type="text/javascript" src="http://fastly.ink.sapo.pt/3.1.10/js/autoload.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
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
                        <form  class="ink-form ink-formvalidator" id="employee" role="form" method="POST" action="/cretin_app_analytics/server.php/reset-password">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="control-group align-center">
                                <label for="userid" id="userid" class="align-right" style="font-size: 16px;">メールにパスワードを送ります。</label>
                                <div class="control">
                                    <input type="text" data-rules="required" placeholder="ユーザーID" id="userid" class="form-control" name="userid">
                                </div>
                            </div>
                            <input type="submit" value="パスワードをリセットする" id="reset" class="ink-button blue align-center">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>