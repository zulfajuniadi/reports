@if($menu['is_enabled']==1)
@if($menu['parent_id'])
<li class="dropdown-item dropdown">
@else
<li class="nav-item dropdown">
@endif
    @if(count($menu['nodes']) > 0)
        @if($menu['parent_id'])
        @else
            <a class="nav-link dropdown-toggle" id="{{$menu['id']}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$menu['title']}}</a>
            <ul class="dropdown-menu" aria-labelledby="{{$menu['id']}}">
            @foreach($menu['nodes'] as $menu)
                @include('partials.menu-item')
            @endforeach
            </ul>
        @endif
    @elseif($menu['type'] == 'link')
        <a class="nav-link" href="{{$menu['href']}}">{{$menu['title']}}</a>
    @elseif($menu['type'] == 'report')
        @if($menu['parent_id'])
        <a class="dropdown-item" href="{{route('report', $menu['href'])}}">{{$menu['title']}}</a>
        @else
        <a class="nav-link" href="{{route('report', $menu['href'])}}">{{$menu['title']}}</a>
        @endif
    @endif
</li>
@endif