
<nav class="nav-deep nav-deep-dark nav-deep-hover fs--15 pb-5">
    <ul class="nav flex-column">

{{--        <li class="nav-item active">--}}
{{--            <a class="nav-link js-ajax" href="/dashboard">--}}
{{--                <i class="fi fi-menu-dots"></i>--}}
{{--                <b>Dashboard</b>--}}
{{--            </a>--}}
{{--        </li>--}}

        @foreach(Gecche\Cupparis\Menus\Facades\Menus::getMenuData(null,true) as $key => $menu)
            @if (count($menu['items']) > 0)
                <li c-item-menu class="nav-item">
                    <a class="nav-link bg-light rounded" href="#">
										<span class="group-icon float-end">
											<i class="fi fi-arrow-end-slim"></i>
											<i class="fi fi-arrow-down-slim"></i>
										</span>
                        <i class="fi fi-code"></i>
                        <b>{{$key}}</b>
                        {{--                                <span class="badge badge-warning float-end fs--11 mt-1">{{$key}}</span>--}}
                    </a>

                    <ul class="nav flex-column ml-3">
                        @foreach($menu['items'] as $voce)
                            <li class="nav-item">
                                {{--                                        <pre>{{print_r($voce,true)}}</pre>     js-ajax --}}
                                <a class="nav-link " href="#{{\Illuminate\Support\Arr::get($voce,'vuepath','/')}}">
                                    {{\Illuminate\Support\Arr::get($voce,'nome','nonome')}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    </ul>
</nav>

