@extends('layouts.master')
@section('content')
    <h1>Login</h1>
    <form method="POST" action="/login" class="form-group">
        @csrf
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" class="form-control" requried>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" class="form-control" requried>
        <button type="submit" name="submit" class="btn btn-primary">Login</button>
    </form>
    @include('layouts.errors')
    @endsection
