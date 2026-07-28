<div>
    @foreach($item as $index => $innerItem)
        @canany($innerItem['gate'],$innerItem['policy_class'])
            <a
                class="{{ $aClassByDepth[$innerItem['has_child']][$depth] }} {{ (!$innerItem['has_child'] && $depth===0) ? 'hsa-link' : '' }}"
                @if($depth===0 || $innerItem['has_child'])
                    data-bs-toggle="{{ $aToggleByDepth[$depth] }}" @endif
                href="{{ (!$innerItem['has_child'] && $innerItem['route'] != null) ? (\Route::has($innerItem['route']) ? route($innerItem['route']) : $innerItem['route']) : '#' }}">
                @if($depth === 0)
                    <i class="side-menu__icon {{ $innerItem['icon'] ?? '' }}"></i>
                @endif
                @if($innerItem['has_child'] || $depth===0)
                    <span class="{{ $spanClassByDepth[$depth] }}">{{ $innerItem['title'] }}</span>
                @else
                    {{ $innerItem['title'] }}
                @endif
                @if($depth ===0 && isset($innerItem['has_badge']) && $innerItem['has_badge'] && $this->badgeCount($innerItem['badge']['class']) > 0)
                    <span
                        class="{{ $innerItem['badge']['type']->value ?? '' }}">{{ $this->badgeCount($innerItem['badge']['class']) }}
                    </span>
                @endif
                @if($innerItem['has_child'])
                    <i class="{{ $angleClassByDepth[$depth] }} fa fa-angle-right"></i>
                @endif
            </a>
            @if($innerItem['has_child'])
                <ul class="{{ $ulClassByDepth[$depth] }}">
                    <livewire:admin::component.menu-item :item="$innerItem['children']" :depth="$nextDepth"/>
                </ul>
            @endif
        @endcanany
    @endforeach
</div>
