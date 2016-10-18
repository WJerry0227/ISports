@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
        <div id="jquery-accordion-menu" class="jquery-accordion-menu white">
        <div class="jquery-accordion-menu-header"></div>
        <ul id="demo-list">
           <li class="active"><a href="#"><i class="fa fa-home"></i>My Sports</a>
                <ul class="submenu white"><li><a href={{ url('/sports/sportsmanagement') }}>Sports Management</a></li></ul>
           </li>
            <li><a href="#"><i class="fa fa-user"></i>Body Management</a>
                <ul class="submenu white">
                <li><a href="#">Height Weight</a></li>
                <li><a href={{ url('/sports/bloodpressure') }}>Blood Pressure</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fa fa-file-image-o"></i>Sleep State</a>
                <ul class="submenu white">
                    <li><a href="#">Sleep Analysis</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fa fa-suitcase"></i>Sports Data</a>
                <ul class="submenu white">
                    <li><a href="#">Walking</a></li>
                    <li><a href="#">Running</a></li>
                    <li><a href="#">Biking</a>
                </ul>
            </li>
           
        </ul>
        </div>
    </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                @yield('sportsmanagement')
                @yield('heightweight')
                @yield('bloodpressure')
                @yield('sleepanalysis')
                @yield('waiking')
                @yield('running')
                @yield('biking')
            </div>
        </div>
    </div>
    
</div>

<script src="/js/lib/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="/js/lib/jquery-accordion-menu.js" type="text/javascript"></script>
<script type="text/javascript">
(function($) {
$.expr[":"].Contains = function(a, i, m) {
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
})(jQuery); 
jQuery("#jquery-accordion-menu").jqueryAccordionMenu();

</script>


<link href="/css/lib/accordion-menu/jquery-accordion-menu.css" rel="stylesheet" type="text/css" />
<link href="/css/lib/accordion-menu/font-awesome.css" rel="stylesheet" type="text/css" />

@endsection
