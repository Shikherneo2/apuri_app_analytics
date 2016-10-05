<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/cretin_app_analytics/public/css/login.css" />
    <link rel="stylesheet" type="text/css" href="/cretin_app_analytics/public/css/login1.css" />
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
   
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-ie.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-flex.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/ink-legacy.css">
    <link rel="stylesheet" href="/cretin_app_analytics/public/css/font-awesome.css">
    <style>
        body {
            background: #e1c192 url(/cretin_app_analytics/public/images/background.jpg);
        }
    </style>
</head>
<body>
<div class="container">

    @if (count($errors) > 0)
        <div class="align-center" >
            <div style="color:#b71c1c;text-align:center;background-color:#E5E5E5;display:inline-block;margin-top:10px;padding-right:10px;padding-top:5px;border-radius:4px;">
                <strong>おっとっと!</strong> リクエスト処理中に問題が発生しました.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="main">
        <form class="form-2" role="form" method="POST" action="{{ action('Auth\AuthController@postLogin') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <h1><span class="log-in">ログイン</span></h1>
            <p class="float">
                <label for="login"><i class="fa fa-user"></i> &nbsp;ユーザ ID</label>
                <input type="text" name="userid" placeholder="ユーザ ID">
            </p>
            <p class="float">
                <label for="password"><i class="fa fa-lock"></i> &nbsp;パスワード</label>
                <input type="password" name="password" placeholder="パスワード" class="showpassword">
            </p>
            <p class="clearfix">
                <input type="submit" style="margin-left:0px;" name="submit" value="ログイン"> <a href="/cretin_app_analytics/server.php/reset_password" style="font-size:14px;margin-left:17px;">パスワードを忘れた場合はこちら</a>
            </p>
        </form>
    </section>
</div>
</body>
</html>