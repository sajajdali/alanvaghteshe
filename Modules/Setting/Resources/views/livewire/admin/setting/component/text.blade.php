<div>
    <div class="form-group">
        <label for="text_{{ $meta->value }}">{{ $meta->getName() }}</label>
        <input type="text"
               class="form-control disable_button_150"
               id="text_{{ $meta->value }}"
               placeholder="مقدار..."
               wire:model.live="textValue">
    </div>
    @if($meta->getDescription())
        <blockquote>
            {!!  $meta->getDescription() !!}
        </blockquote>
        <hr>
    @endif
</div>
