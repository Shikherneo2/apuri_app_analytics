@extends('project.general.navigation')

@section('content')
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

<div class="button-group align-center push-center" id="home_buttons">
  
@if($type == "3")
    <a href="manage-franchise" class="ink-button black push-center align-center">
        <i class="fa fa-chain fa-2x" ></i><br/>
        フランチャイズを<br/> 管理する
    </a>
@endif

@if($type == "3" || $type == "4")
    <a href="manageshop" class="ink-button black push-center align-center">
        <i class="fa fa-shopping-cart fa-2x" ></i><br/>
        ショップを<br/> 管理する
    </a>
@endif
@if($type == "3" || $type=="4")
    <a href="manage-shopkeeper" class="ink-button black push-center align-center">
        <i class="fa fa-users fa-2x" ></i><br/>店主を<br/>
        管理する
    </a>
@endif
@if($type == "3")
    <a href="manage-shop_employee" class="ink-button black push-center align-center">
        <i class="fa fa-briefcase fa-2x" ></i><br/>店員を<br/>
        管理する
    </a>
    
    <a href="manage-app" class="ink-button black push-center align-center">
        <i class="fa fa-mobile-phone fa-2x" ></i><br/>アプリを<br/> 管理する
    </a>
@endif
</div>
@endsection