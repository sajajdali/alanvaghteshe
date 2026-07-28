<div>
    <ul class="side-menu">
        @foreach($menuItems as $menu)
            @canany($menu['gate'],$menu['policy_class'])
                @if(isset($menu['has_divider']) && $menu['has_divider'])
                    <li>
                        <h3>{{ $menu['title'] ?? '' }}</h3>
                    </li>
                @endif
                <li class="slide">
                    <livewire:admin::component.menu-item :item="$menu['children']" :depth="0"/>
                </li>
            @endcanany
        @endforeach
    </ul>
</div>
