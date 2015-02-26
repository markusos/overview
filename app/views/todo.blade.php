@extends("layouts.master")

@section("content")

    @if (isset($error))
        <div id="error" class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            {{ $error }}
        </div>
    @endif

    <div id="loading">
        <button class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</button>
    </div>

    <div id="connect">
        <a href="/oauth/register"><button class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-log-in"></span> Connect</button></a>
    </div>

    <div id="todos"></div>
@stop