<?php
if (isset($_SESSION)) {
    
} else {
    session_start();
}
date_default_timezone_set('Asia/Colombo');
?>
@extends('Templates/WiTemplate')

@section('title')
Laboratory Configurations
@stop

@section('head')

@stop

@section('body')
<h3 class="pageheading">Laboratory Configurations</h3>
<br/>
<br/>
<blockquote>
<a href="addpconfig" class="configbtns">Patient Register Form</a>
<br/>
<a href="reportconfig" class="configbtns">Testing Report Configurations</a>


</blockquote>

@stop