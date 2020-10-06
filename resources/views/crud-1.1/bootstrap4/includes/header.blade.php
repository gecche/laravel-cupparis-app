
<nav class="navbar navbar-light" style="background-color: #e3f2fd;">
    @foreach(Gecche\Cupparis\Menus\Facades\Menus::getMenuData('Header',true) as $key => $menu)
        @foreach($menu['items'] as $item)
            <a href="{{$item['vuepath']}}">{{$item['nome']}}</a>
        @endforeach
    @endforeach
    <a href="/dashboard">Dashboard</a>
    <a href="/prove-vue">Prove</a>
</nav>
