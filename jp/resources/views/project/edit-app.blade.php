@extends('project.general.navigation')

@section('specificScripts')
<script src="/cretin_app_analytics/public/js/SimpleAjaxUploader.js"></script>
<script>
    function escapeTags( str ) {
        return String( str )
                .replace( /&/g, '&amp;' )
                .replace( /"/g, '&quot;' )
                .replace( /'/g, '&#39;' )
                .replace( /</g, '&lt;' )
                .replace( />/g, '&gt;' );
    }

    $(document).ready(function() {

        var btn = document.getElementById('uploadBtn');

        var uploader = new ss.SimpleUpload({
            button: btn,
            url: '/cretin_app_analytics/server.php/upload_logo_update_app',
            name: 'image',
            multipart: true,
            hoverClass: 'hover',
            focusClass: 'focus',
            customHeaders: { 
                'X-CSRF-TOKEN': $("#_token").val()
            },
            data:{"app_id":"{{ $app['id'] }}" },
            responseType: 'json',
            startXHR: function() {
                
            },
            onSubmit: function() {
                $("#uploadBtn").val("アップロードをしてる.....");
                $("#msgBox").text("");
                $("#msgBox").prepend('<img src="/cretin_app_analytics/public/images/loader.gif" id="loader_img" height="30">');    
                
            },
            onComplete: function( filename, response ) {
                $("#uploadBtn").val("ロゴを変更する");
                $("#loader_img").remove();

                if ( !response ) {
                    msgBox.innerHTML = 'おっとっと! アップロードできない。';
                    return;
                }
                else{                
                    if ( response.error != "1" ){ 
                        msgBox.innerHTML = '<strong>' + escapeTags( filename ) + '</strong>' + ' をアップロードしました。';
                        $("#uploaded_image").remove();
                        $("#msgBox").append('<img id="uploaded_image" height=100 src="/cretin_app_analytics/public/uploads/'+response.filename+'">');
                    }
                    else   
                        msgBox.innerHTML = 'おっとっと! アップロードできない。';
                }
            },
            onError: function() {
                msgBox.innerHTML = 'おっとっと! アップロードできない。';
            }
        });
    });
</script>
@endsection

@section('content')
<style>
    #reset {
        margin-left:10px;
        width:130px;
    }
    #uploaded_image{
        margin-top:10px;
    }
</style>
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

    <form class="ink-form ink-formvalidator" method="post" action="/cretin_app_analytics/server.php/editApp">
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="id" value='{{ $app["id"] }}'>
        <div class="control-group required">
            <label for="app_name">アプリの名前</label>
            <div class="control">
                <input type="text" data-rules="required|text[true,false]" name="name" id="app_name" value='{{ $app["name"] }}'/>
            </div>
        </div>
        <div class="control-group required">
            <label for="ID">パッケージの名前</label>
            <div class="control">
                <input type="text" name="package_name" id="ID" data-rules="required" value='{{ $app["package_name"] }}'/>
                <select id="option" name="type">
                    <option value="1" @if( $app["type"]==1 ) selected="selected" @endif>iOS</option>
                    <option value="2" @if( $app["type"]==2 ) selected="selected" @endif>アンドロイド</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="control">
                <input type="button" value="ロゴの　画像を変更する" id="uploadBtn" style="width:300px;" accept="image/*">
                <br>
                <div id="msgBox" style="display:inline-block;"></div>
                @if( $app["img_src"] != "")
                    <img id="uploaded_image" height=100 src="/cretin_app_analytics/public/uploads/{{$app['img_src']}}">
                @endif
            </div>
        </div>
        <div class="control-group required">
            <label for="store_url">ストア　リンク:</label>
            <div class="control">
                <input type="url" name="store_link" id="store_url" data-rules="required" value='{{ $app["store_link"] }}'/>
            </div>
        </div>
        <div class="control-group">
            <label for="desc">説明　:　</label>
            <div class="control">
                <textarea type="text" name="desc" id="desc" >{{ $app["desc"] }}</textarea>
            </div>
        </div>
        <div class="button-group">
            <input type="submit" name="sub" id="update" value="セーブをする" class="ink-button black push-right" />
            <input type="reset" name="sub" id="reset" value="リセットする" class="ink-button black push-right" />
        </div>

        <a href="/cretin_app_analytics/server.php/viewapps">
            <input type="button" name="sub" id="applist" value="アプリのリスト" class="ink-button red push-left" />
        </a>
    </form>

@endsection