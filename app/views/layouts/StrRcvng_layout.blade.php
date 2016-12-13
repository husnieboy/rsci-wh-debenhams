<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>eWMS Debenhams</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="shortcut icon" href="{{asset('resources/img/deb.ico')}}" />
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
<!--link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600"
        rel="stylesheet"-->

{{ HTML::style('resources/css/font-awesome.css') }}
{{ HTML::style('resources/css/datepicker.css') }}
{{ HTML::style('resources/css/style.css') }}
{{ HTML::style('resources/css/pages/dashboard.css') }}
{{ HTML::style('resources/css/pages/signin.css') }}
{{ HTML::style('resources/css/pages/reports.css') }}
{{ HTML::script('resources/js/jquery-1.7.2.min.js') }}
{{ HTML::script('resources/js/jquery-1.7.2.min.js') }}

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="navbar-brand" id="logo" href="{{ URL::to('/') }}"></a>
      <!-- <a class="navbar-brand" id="oldnavy" href="{{ URL::to('/') }}"></a> -->
      <a class="brand" href="{{ URL::to('/') }}">{{ $title_brand }}</a>
      <div class="nav-collapse">
        <ul class="nav pull-right">
          @if(Auth::check())
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="icon-user"></i> {{Auth::user()->username}} <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="{{ URL::to('user/profile') }}">{{ $menu_profile }}</a></li>
              <li><a href="{{ URL::to('user/change_password') }}">{{ $menu_change_password }}</a></li>
              <li><a href="{{ URL::to('users/logout') }}">{{ $menu_logout }}</a></li>
            </ul>
          </li>
          @endif
        </ul>
      </div>
      <!--/.nav-collapse -->
    </div>
    <!-- /container -->
  </div>
  <!-- /navbar-inner -->
</div>
<!-- /navbar -->
@if(Auth::check())
<div class="subnavbar">
  <div class="subnavbar-inner">
    <div class="container">
      <ul class="mainnav">
        

    
  

    

        @if ( CommonHelper::valueInArray('CanAccessStoreOrders', $permissions)  
    )
    <li class="dropdown">
      <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon-inbox"></i>
        <span>{{ $menu_str_receiving }}</span>
        <b class="caret"></b>
      </a>

      <ul class="dropdown-menu">
          @if ( CommonHelper::valueInArray('CanAccessStoreOrders', $permissions) )
            <li><a href="{{ URL::to('store_order') }}">{{ $menu_store_order }}</a></li>
          @endif
          
   
        
      </ul>
    </li>
    @endif  <!--end if drop down for store receiving-->

		 
 
        <li class=" ">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
			 
				<span> </span>
				<b class="caret"></b>
			</a>

		 
		</li>
	 
      </ul>
    </div>
    <!-- /container -->
  </div>
  <!-- /subnavbar-inner -->
</div>
@endif
<!-- /subnavbar -->
<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="span12">
          @if(Session::has('message'))
          	<div class="alert alert-info">
		    	<button class="close" data-dismiss="alert" type="button">&times;</button>
		    	{{ Session::get('message') }}
		    </div>
          @endif
          {{ $content }}
          <!-- /widget -->
        </div>
        <!-- /span12 -->
      </div>
      <!-- /row -->
    </div>
    <!-- /container -->
  </div>
  <!-- /main-inner -->
</div>
<!-- /main -->
<div class="footer collapse">
  <div class="footer-inner">
    <div class="container">
      <div class="row">
        <div class="span12"></div>
        <!-- /span12 -->
      </div>
      <!-- /row -->
    </div>
    <!-- /container -->
  </div>
  <!-- /footer-inner -->
</div>
<!-- /footer -->
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{{ HTML::script('resources/js/excanvas.min.js') }}
{{ HTML::script('resources/js/chart.min.js') }}
{{ HTML::script('resources/js/bootstrap.js') }}
{{ HTML::script('resources/js/bootstrap-datepicker.js') }}
{{ HTML::script('resources/js/full-calendar/fullcalendar.min.js') }}

{{ HTML::script('resources/js/base.js') }}
</body>
</html>
