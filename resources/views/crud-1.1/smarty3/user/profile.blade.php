@extends('layouts.app')

@section('content')

    <div class="col-12 d-lg-flex">
        <div class="w-100 align-self-center text-center-md text-center-xs py-2">


            <!-- optional class: .form-control-pill -->
            <form novalidate action="{{ route('user.profile-update') }}"
                  method="POST"
                  class="bs-validate p-5 py-6 rounded d-inline-block bg-white text-dark w-100">

            @csrf

            <!--
                <p class="text-danger">
                    Ups! Please check again
                </p>
                -->
                <h4 class="font-weight-light text-center mb-3">Modifica il tuo profilo</h4>


                @foreach ($errors->all() as $message)

                    <!-- cookie alert -->
                    <div class="alert bg-danger-soft text-dark p-3 my-2 b-0 rounded d-inline-block w-100 ">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span class="fi fi-close" aria-hidden="true"></span>
                        </button>
                        <strong>{{ $message }}</strong>
                    </div>

                @endforeach


                <div class="form-label-group mb-3">
                    <input required placeholder="Email" id="email"
                           type="email" name="email" class="form-control"
                           value="{{ old('email') ?? $user->email }}"
                    >
                    <label for="email">Username/Email</label>
                </div>

                <div class="form-label-group mb-3">
                    <input required placeholder="Username" id="name"
                           name="name" class="form-control"
                           value="{{ old('name') ?? $user->name }}"
                    >
                    <label for="email">Username</label>
                </div>


                <div class="form-label-group mb-3">
                    <input placeholder="Password" id="password" name="password" type="password"
                           class="form-control">
                    <label for="password">Password</label>
                </div>

                <div class="form-label-group mb-3">
                    <input placeholder="{{ __('Confirm Password') }}" id="password_confirmation"
                           name="password_confirmation" type="password" class="form-control"
                    >
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                </div>

                <div class="form-label-group mb-3">
                    <input required placeholder="Nome" id="T_UTENTE_NOME"
                           name="T_UTENTE_NOME" class="form-control"
                           value="{{ old('T_UTENTE_NOME') ?? $user->T_UTENTE_NOME }}"
                    >
                    <label for="T_UTENTE_NOME">Nome</label>
                </div>

                <div class="form-label-group mb-3">
                    <input required placeholder="Nome" id="T_UTENTE_COGNOME"
                           name="T_UTENTE_COGNOME" class="form-control"
                           value="{{ old('T_UTENTE_COGNOME') ?? $user->T_UTENTE_COGNOME}}"
                    >
                    <label for="T_UTENTE_COGNOME">Cognome</label>
                </div>

                <div class="row">

                    <div class="col-12 col-md-6 mt-4">
                        <button type="submit"
                                class="btn {{$layoutGradientColor}} btn-block transition-hover-top text-white border-0">
                            <i class="fa fa-check"></i><span class="text-white">Salva</span>
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

@stop
