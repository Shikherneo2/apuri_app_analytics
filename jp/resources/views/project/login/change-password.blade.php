@extends('project.general.navigation')

@section('content')
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

                            @if (Session::get('reset'))
                                <form class="ink-form" role="form" method="POST" action="/cretin_app_analytics/server.php/change-password-reset">
                            @else
                                <form class="ink-form ink-formvalidator" id="employee" role="form" method="POST" action="/cretin_app_analytics/server.php/change-password">
                            @endif
    
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    @if (!Session::get('reset'))
                                        <div class="control-group align-center">
                                            <label for="old_password">現在のパスワードを入力してください</label>
                                            <div class="control">
                                               <input type="password" class="form-control" name="old_password" id="old_password">
                                            </div>

                                    @else
                                            <input type="hidden" name="key" value="{{ Session::get('reset') }}">
                                    @endif

                                            <label for="new_password">新しいパスワードを入力してください</label>
                                            <div class="control">
                                                <input type="password" class="form-control" name="new_password" id="new_password">
                                            </div>

                                            <label for="new_password_confirmation">新しいパスワードを再入力してください</label>
                                            <div class="control">
                                                <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation">
                                            </div>
                                        </div>


                                        <input type="submit" value="パスワードをリセットする" id="reset" class="ink-button green push-center" />
                                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
