<tr class="{{ $bg }}">
    <td>{{ $cat->id }}</td>
    <td>
        @for($i=0;$i<$depth;$i++)
            <span class="mx-1">|—</span>
        @endfor
        {{ $cat->name }}
    </td>
    <td>
        {{ number_format($cat->exercises->count()) }}
    </td>
    <td>
        @canany(['update','delete'],$cat)
            <div class="btn-group mt-2 mb-2">
                <button type="button" class="btn btn-primary dropdown-toggle"
                        data-bs-toggle="dropdown">
                    عملیات <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @can('update',$cat)
                        <li><a href="{{ route('admin.exercise_body_category.edit',$cat) }}">ویرایش</a>
                        </li>
                    @endcan
                    @can('delete',$cat)
                        @unless($cat->has_children)
                            <li><a class="delete_confirm_alert"
                                   data-label="دسته"
                                   data-id="{{ $cat->id }}"
                                   href="">حذف</a>
                            </li>
                        @else
                            <li><a class="admin_sweet_alert op-0-4"
                                   data-title="خطا"
                                   data-type="error"
                                   data-description="برای حذف این دسته ابتدا دسته‌های فرزند آن را حذف یا به دسته دیگیری منتقل کنید."
                                   href="#">حذف</a>
                            </li>
                        @endunless
                    @endcan
                </ul>
            </div>
        @else
            <div class="btn-group mt-2 mb-2">
                <button type="button" class="btn btn-default dropdown-toggle"
                        data-bs-toggle="dropdown">
                    عملیات <span class="caret"></span>
                </button>
            </div>
        @endcanany
    </td>
</tr>
@if($cat->has_children)
    @foreach($cat->children as $child)
        @include('exercise::livewire.admin.exercise-body-category.exercise-body-category-list-item',['cat'=>$child,'depth'=>$depth+1,'bg' => $bg ])
    @endforeach
@endif
