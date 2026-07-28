<div>
    <div class="form-group">
        <label for="select_{{ $meta->value }}">{{ $meta->getName() }}</label>
        <select
            class="form-select form-input"
            wire:model.live="selectValue"
            name="select_{{ $meta->value }}"
            id="select_{{ $meta->value }}">
            <option value="">انتخاب کنید</option>
            @foreach($meta->options() as $key => $title)
                <option value="{{ $key }}">{{ $title }}</option>
            @endforeach
        </select>
    </div>
    @if($meta->getDescription())
        <blockquote>
            {!!  $meta->getDescription() !!}
        </blockquote>
        <hr>
    @endif
</div>
