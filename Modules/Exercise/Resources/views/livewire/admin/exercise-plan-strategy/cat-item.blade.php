<option value="{{ $cat->id }}" @if(in_array($cat->id,$selected,false)) SELECTED @endif>
    @for($i=0;$i<$level;$i++)
        |--&nbsp;
    @endfor
    {{ $cat->name }}
</option>
@if($cat->children)
    @foreach($cat->children as $child)
        @include('exercise::livewire.admin.exercise-plan-strategy.cat-item',['cat'=>$child,'level'=>$level+1,'$selected'=>$selected])
    @endforeach
@endif
