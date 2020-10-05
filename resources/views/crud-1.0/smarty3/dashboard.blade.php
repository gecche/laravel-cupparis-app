@extends('layouts.app')
@section('content')
    {{--        <div class="row">--}}
    {{--            <div class="col-2">--}}
    {{--                @include('includes.sidebar')--}}
    {{--            </div>--}}

    {{--            <div class="col-10">--}}
    {{--                <div  id="component-area"></div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    <div id="app" class="row">

        {{--<div class="col-12 col-xl-6 mb-5">--}}


            {{--<!-- card rating -->--}}
            {{--<div class="position-relative shadow-lg bg-white rounded-xl p-5 p-md-3 py-xl-5 p-4-xs z-index-2">--}}

                {{--<h6 class="text-center font-weight-normal text-green-800">--}}
                    {{--Telefonate in programma--}}
                {{--</h6>--}}

                {{--<div class="text-center mt-5">--}}


                    {{--<div id="tabUserListContent" class="portlet-body max-h-500 tab-content">--}}

                        {{--<!-- tab 1 -->--}}
                        {{--<div class="h-100 tab-pane show active" id="usertab_1" aria-labelledby="usertab-1">--}}


                            {{--<!----}}

                                {{--IMPORTANT--}}
                                {{--The "action" hidden input is updated by "selected items" action--}}
                                    {{--data-js-form-advanced-hidden-action-id="..."--}}
                                    {{--data-js-form-advanced-hidden-action-value="..."--}}

                                {{--PHP example of data backed processing:--}}

                                    {{--if($_POST['action'] === 'delete') {--}}

                                        {{--foreach($_POST['item_id'] as $item_id) {--}}
                                            {{--// ... delete $item_id from database--}}
                                        {{--}--}}

                                    {{--}--}}

                            {{---->                 <!-- task list -->--}}
                            {{--<div id="task_list_today"--}}
                                 {{--class="max-h-500 scrollable-vertical scrollable-styled-dark align-self-baseline h-100 max-h-500 w-100">--}}

                            {{--@forelse ($data['nextTelefonate'] as $telefonata)--}}

                                {{--<!-- item -->--}}
                                    {{--<div class="border-bottom border-light">--}}
                                        {{--<div class="d-flex align-items-center p-3">--}}


                                            {{--<div class="w--20 fs--14 text-success font-weight-light text-align-start">--}}
                                                {{--<i class="fa fa-phone"></i>--}}
                                            {{--</div>--}}
                                            {{--<div class="flex-fill text-truncate line-height-1">--}}
                                                {{--<div class="text-muted mb-2">--}}
                                                    {{--<div class="border-bottom bw-2 p-1 m-2">--}}
                                                        {{--{!! $telefonata['referente'] !!}--}}
                                                    {{--</div>--}}

                                                    {{--<a target="_blank" href="{!! $telefonata['href'] !!}">--}}
                                                    {{--<span class="text-green-800">--}}
                                                        {{--{!! $telefonata['tel'] !!}--}}
                                                    {{--</span>--}}
                                                    {{--</a>--}}
                                                {{--</div>--}}
                                                {{--<div class="fs--13 d-block text-black">--}}
                                                    {{--{!! $telefonata['istituto'] !!}--}}
                                                {{--</div>--}}
                                            {{--</div>--}}

                                            {{--<div class="w--180 fs--14 text-danger font-weight-light text-align-end">--}}
                                                {{--{!! $telefonata['orario'] !!}--}}
                                            {{--</div>--}}

                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<!-- /item -->--}}

                                {{--@empty--}}
                                    {{--<div>Nessuna telefonata in programma</div>--}}

                                {{--@endforelse--}}


                            {{--</div>--}}
                            {{--<!-- /task list -->--}}


                        {{--</div>--}}


                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}
            {{--<!-- /card rating -->--}}


        {{--</div>--}}



    </div>
@stop
