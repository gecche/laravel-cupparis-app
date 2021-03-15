@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">GAP</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Amministrazione
{{--                    You are logged in! <a href="/dashboard">Dashboard</a>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
