<option value="{{ $cat->id }}" @if(in_array($cat->id,$category,false)) SELECTED @endif>
    @for($i=0;$i<$level;$i++)
        |--&nbsp;
    @endfor
    {{ $cat->name }}
</option>
@if($cat->children)
    @foreach($cat->children as $child)
        @include('exercise::livewire.admin.exercise.exercise-create-or-update-category-item',['cat'=>$child,'level'=>$level+1])
    @endforeach
@endif
