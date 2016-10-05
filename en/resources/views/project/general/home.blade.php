@extends('project.general.navigation')

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

<div class="button-group align-center push-center" id="home_buttons">
  
@if($type == "3")
    <a href="manage-franchise" class="ink-button black push-center align-center">
        <i class="fa fa-chain fa-2x" ></i><br/>
        Manage<br/> Franchise
    </a>
@endif

@if($type == "3" || $type == "4")
    <a href="manageshop" class="ink-button black push-center align-center">
        <i class="fa fa-shopping-cart fa-2x" ></i><br/>
        Manage<br/> Shops
    </a>
@endif
@if($type == "3" || $type=="4")
    <a href="manage-shopkeeper" class="ink-button black push-center align-center">
        <i class="fa fa-users fa-2x" ></i><br/>Manage<br/>
        Shopkeepers
    </a>
@endif
@if($type == "3")
    <a href="manage-shop_employee" class="ink-button black push-center align-center">
        <i class="fa fa-briefcase fa-2x" ></i><br/>Manage<br/>
        Employees
    </a>
    
    <a href="manage-app" class="ink-button black push-center align-center">
        <i class="fa fa-mobile-phone fa-2x" ></i><br/>Manage<br/> Apps
    </a>
@endif
</div>
@endsection