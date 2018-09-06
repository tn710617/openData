@extends('layouts.master')
@section('content')
    @if(Auth::check() && Auth::user()->type == 'admin')
        <h1>Hello <span style="color:blue"> {{ Auth::user()->name }}</span>! Welcome back our administrator!</h1>
    @endif
    @if(Auth::check() && !(Auth::user()->type == 'admin'))
        <h1>Hello <span style="color:blue"> {{ Auth::user()->name }}</span>! Is there anything I can help?</h1>
    @endif
    @guest
        <h1>Hello! Please log in your account for more services</h1>
    @endguest
    @stop
