@extends('layouts.login')

@section('content')

    @include('auth.left')


    <div class="col-12 col-lg-7 d-lg-flex">
        <div class="w-100 align-self-center text-center-md text-center-xs py-2">


            <!-- optional class: .form-control-pill -->
            <form novalidate action="{{ route('login') }}"
                  method="POST"
                  class="bs-validate p-5 py-6 rounded d-inline-block bg-white text-dark w-100 max-w-600">

            @csrf

            <!--
                <p class="text-danger">
                    Ups! Please check again
                </p>
                -->
                <h4 class="font-weight-light text-center mb-3">Login</h4>


                @error('email')
                <!-- cookie alert -->
                <div class="alert bg-danger-soft text-dark p-3 my-2 b-0 rounded d-inline-block w-100 max-w-600">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span class="fi fi-close" aria-hidden="true"></span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
                @enderror

                @error('password')
                <div class="alert bg-danger-soft text-dark p-3 my-2 b-0 rounded d-inline-block w-100 max-w-600">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span class="fi fi-close" aria-hidden="true"></span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
                @enderror

                <div class="form-label-group mb-3">
                    <input required placeholder="Email" id="email"
                           name="email" class="form-control"
                           value="{{ old('email') }}"
                    >
                    <label for="email">Username/Email</label>
                </div>

                <div class="input-group-over">
                    <div class="form-label-group mb-3">
                        <input required placeholder="Password" id="password" name="password" type="password"
                               class="form-control">
                        <label for="password">Password</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="btn fs--12">
                            PASSWORD DIMENTICATA?
                        </a>
                    @endif

                </div>

                <div class="row">

                    <div class="col-12 col-md-6 mt-4">
                        <button type="submit"
                                class="btn {{$layoutGradientColor}} btn-block transition-hover-top text-white border-0">
                            <span class="text-white">Sign In</span>
                        </button>
                    </div>

                </div>

                {{--                <div class="row">--}}

                {{--                    <div class="col-12">--}}
                {{--                        Contatti ESPAD usa i cookies per una migliore esperienza! <a href="#!" class="link-muted">Approfondisci</a>--}}

                {{--                    </div>--}}
                {{--                </div>--}}
            </form>


            {{--            <!-- cookie alert -->--}}
            {{--            <div class="alert bg-white text-dark p-3 my-2 b-0 rounded d-inline-block w-100 max-w-600">--}}
            {{--                <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
            {{--                    <span class="fi fi-close" aria-hidden="true"></span>--}}
            {{--                </button>--}}
            {{--                Contatti ESPAD usa i cookies per una migliore esperienza! <a href="#!" class="link-muted">Approfondisci</a>--}}
            {{--            </div>--}}


        </div>
    </div>

    <hr/>

@endsection
