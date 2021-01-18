<!--

					SIDEBAR

					Note: overlay-opacity-* should match the footer (same opacity)
				-->

<aside id="aside-main"
       class="aside-start {{$layoutGradientColor}} font-weight-light aside-hide-xs d-flex align-items-stretch justify-content-lg-between align-items-start flex-column">

    <!--
        LOGO
        visibility : desktop only
    -->
    <div class="align-self-baseline w-100">
        <div class="clearfix d-flex justify-content-between bg-diff bg-white mb-0">

            <!-- Logo : height: 60px max -->
            <a class="w-100 align-self-center navbar-brand px-3 p-1" href="/">
                <span data-gfont="Sriracha" class="text-dark">
                    <img src="{!! Theme::url('images/logo.png') !!}" width="55" height="40"
                         alt="">{{env('APP_NAME')}}
                </span>
            </a>
        </div>

    </div>
    <!-- /LOGO -->


    <div class="aside-wrapper scrollable-vertical scrollable-styled-light align-self-baseline h-100 w-100">

        <!--

            All parent open navs are closed on click!
            To ignore this feature, add .js-ignore to .nav-deep

            Links height (paddings):
                .nav-deep-xs
                .nav-deep-sm
                .nav-deep-md  	(default, ununsed class)

            .nav-deep-hover 	hover background slightly different
            .nav-deep-bordered	bordered links
        -->

            <nav class="nav-deep nav-deep-dark nav-deep-dark-contrast nav-deep-indicator-dot nav-deep-hover">

                <ul class="nav flex-column">

                    <li class="nav-item">
                        <a class="nav-link " href="/dashboard">
                            <i class="fi fi-menu-dots"></i>
                            <b>Dashboard</b>
                        </a>
                    </li>


                @foreach(Gecche\Cupparis\Menus\Facades\Menus::getMenuData(null,true) as $key => $menu)
                        @if (count(Arr::get($menu,'items',[])) > 1)
                            {{--                <li c-item-menu class="nav-title mt-5">--}}
                            {{--                    <h6 class="fs--15 mb-1 text-white font-weight-normal">{{$key}}</h6>--}}
                            {{--                </li>--}}

                            <li c-item-menu class="nav-item">
                                <a class="nav-link" href="#">
                                    {{--                        <i class="nav-icon fi fi-database"><!-- main icon --></i>--}}
                                    <span class="group-icon float-end">
                                                <i class="fi fi-arrow-end-slim"></i>
                                                <i class="fi fi-arrow-down-slim"></i>
                                            </span>
                                    {{$key}}
                                </a>
                                <ul class="nav flex-column">
                                    @foreach($menu['items'] as $voce)
                                        <li class="nav-item">
                                            @if (Arr::get($voce,'vuepath'))
                                            <a class="nav-link"
                                                    href="#{{Arr::get($voce,'vuepath','/')}}">
                                                        {{Arr::get($voce,'nome','nonome')}}
                                                </a>
                                            @else
                                                <a class="nav-link"
                                                   href="{{Arr::get($voce,'path','/')}}">
                                                    {{Arr::get($voce,'nome','nonome')}}
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                        @elseif (count(Arr::get($menu,'items',[])) == 1)
                        {{--                <li c-item-menu class="nav-title mt-5">--}}
                        {{--                    <h6 class="fs--15 mb-1 text-white font-weight-normal">{{$key}}</h6>--}}
                        {{--                </li>--}}

                            <li c-item-menu class="nav-item">
                                @if (Arr::get(current($menu['items']),'vuepath'))
                                    <a class="nav-link"
                                    href="#{{Arr::get(current($menu['items']),'vuepath','/')}}">
                                        {{Arr::get(current($menu['items']),'nome','nonome')}}
                                    </a>
                                @else
                                    <a class="nav-link"
                                       href="{{Arr::get(current($menu['items']),'path','/')}}">
                                        {{Arr::get(current($menu['items']),'nome','nonome')}}
                                    </a>
                                @endif
                            </li>

                        @endif
                @endforeach
            </ul>
        </nav>


</div>


</aside>

<!-- /SIDEBAR -->


