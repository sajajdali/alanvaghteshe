<div>
    <div class="form-group">
        <label for="text_{{ $meta->value }}">{{ $meta->getName() }}</label>
        <textarea class="form-control mb-4" placeholder="مقدار" id="text_{{ $meta->value }}" wire:model.live="textValue"
            rows="3" spellcheck="false">{{ $textValue }}</textarea>
    </div>
    @if($meta->getDescription())
        <blockquote>
            {!!  $meta->getDescription() !!}
        </blockquote>
        <hr>
    @endif
</div>
