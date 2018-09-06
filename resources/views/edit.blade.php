@extends('layouts.master')
@section('content')
    <form action="/edit" method="post">
        @csrf
        @method('put')
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" requried>
    <label for="email">Email: </label>
    <input type="email" name="email" id="email" class="form-control" requried value="{{ $user->email }}">
    <label for="password">Current Password: </label>
    <input type="password" name="current_password" id="current_password" class="form-control" requried>
    <label for="new_password">New Password </label>
    <input type="password" name="new_password" id="new_password" class="form-control" requried>
        <label for="new_password_confirmation">New Password Confirmation: </label>
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" requried>
    <button type="submit" class="btn btn-primary">Update</button>
    </form>
    @include('layouts.errors')
@stop
