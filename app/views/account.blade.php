
@extends('layouts.master')

@section('content')
    <h2>Select account: </h2>
    <form action="/authorization" method="POST">
        @foreach ($accounts as $key => $account)
            <p>
                <button class="btn btn-lg btn-primary" name="api" type="submit" value="{{ $key }}">
                    <span class="glyphicon glyphicon glyphicon-home"></span> {{ $account->name }}
                </button>
            </p>
        @endforeach
    </form>
@stop


