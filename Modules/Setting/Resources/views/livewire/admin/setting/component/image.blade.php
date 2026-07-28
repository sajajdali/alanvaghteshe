<div>
    <div class="form-row">
        <label for="thumbnail">{{ $meta->getName() }}</label>
        <div class="input-group">
               <span class="input-group-btn">
                 <a id="select_image_{{ $meta->value }}" data-input="thumbnail_{{ $meta->value }}"
                    class="btn btn-primary image_handler">
                   <i class="fa fa-picture-o"></i> انتخاب تصویر
                 </a>
               </span>
            <input id="thumbnail_{{ $meta->value }}"
                   class="form-control image_input_change"
                   type="text"
                   value="{{ $image }}"
                   name="nolocal">
        </div>
        @if($image !== null)
            <div id="preview-{{ $meta->value }}" style="margin-top:15px;max-height:100px;">
                <img src="{{ $image }}" style="height: 100%">
            </div>
        @endif
    </div>
</div>
