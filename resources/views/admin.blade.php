@extends('layouts.master')
@section('content')
    @if(count($user))

        <form action="/delete" method="post">
            @csrf
{{--            @method('delete')--}}
        @foreach($user as $individual)
            {{--{{ dd($individual['password']) }}--}}
                <fieldset>
                    <h1><legand>Member {{ $individual->email }}</legand></h1>
                <ul>
                    <li>Name: {{ $individual['name'] }}</li>
                    <li>Email: {{ $individual['email'] }}</li>
                    <li>Create: {{ $individual['created_at'] }}</li>
                    <li>Update: {{ $individual['updated_at'] }}</li>
                    <li>Password: {{ $individual['password'] }}</li>
                </ul>
                    <div class="alert alert-danger">
                    <input type="checkbox" name="checked[]" for="checked" value="{{ $individual['id'] }}">
                    <label for="checked">Check me to delete</label>
                        </div>
                 </fieldset>
        @endforeach
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    @endif
@stop
