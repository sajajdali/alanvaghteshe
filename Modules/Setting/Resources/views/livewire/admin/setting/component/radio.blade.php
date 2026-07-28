<div>
    <div class="example mb-5">
        <p class="text-muted">{{ $meta->getName() }}</p>
        @foreach($meta->options() as $key => $option)
            <div class="form-check">
                <input class="form-check-input" type="radio" value="{{ $key }}"
                       wire:model="radioValue"
                       name="radio_{{ $meta->value }}"
                       id="radio_{{ $meta->value }}_{{ $key }}">
                <label class="form-check-label" for="radio_{{ $meta->value }}_{{ $key }}">
                    {{ $option }}
                </label>
            </div>
        @endforeach
    </div>
</div>
