@extends('app')
@section('content')
    <div id="app">
        <c-router c-ref="router" c-content-id="component-area"></c-router>
        <div class="row">
            <div class="col-2">
                @include('includes.sidebar')
            </div>

            <div class="col-10">
                <div id="component-area"></div>
            </div>
        </div>


    </div>
@stop
