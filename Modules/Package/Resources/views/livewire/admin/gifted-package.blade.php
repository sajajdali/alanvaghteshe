<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">بسته‌های رایگان اهدایی</h1>
            <div class="text-muted mt-1">فهرست بسته‌های رایگانی که به کاربران اختصاص داده شده است.</div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <label for="gifted-package-search" class="form-label">جست‌وجوی موبایل یا نام بسته</label>
                <input id="gifted-package-search" class="form-control" wire:model.live.debounce.400ms="search">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap align-middle">
                    <thead><tr><th>#</th><th>کاربر</th><th>موبایل</th><th>بسته</th><th>وضعیت</th><th>شروع</th><th>پایان</th></tr></thead>
                    <tbody>
                    @forelse($packages as $item)
                        <tr wire:key="gifted-package-{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td><a href="{{ route('admin.user.document', $item->user) }}">{{ $item->user?->full_name ?: '---' }}</a></td>
                            <td>{{ $item->user?->mobile ?: '---' }}</td>
                            <td>{{ $item->package?->name ?: '---' }}</td>
                            <td>{!! $item->packageUser?->type?->getAdminBadge() ?? '---' !!}</td>
                            <td>{{ $item->packageUser?->start_at ? verta($item->packageUser->start_at)->format('Y/m/d') : '---' }}</td>
                            <td>{{ $item->packageUser?->end_at ? verta($item->packageUser->end_at)->format('Y/m/d') : '---' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">بسته رایگان اهدایی یافت نشد.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $packages->links() }}
        </div>
    </div>
</div>
