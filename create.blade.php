@extends('layouts.master')
@section('content')
    <h1>Register</h1>
    <form method="POST" action="/register" class="form-group">
        @csrf
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" requried>
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" class="form-control" requried>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" class="form-control" requried>
        <label for="password_confirmation">Password Confirmation: </label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" requried>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    @include('layouts.errors')
@stop

