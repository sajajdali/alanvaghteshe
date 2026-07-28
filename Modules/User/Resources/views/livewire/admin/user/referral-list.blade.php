<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت دعوت از دوستان</h1>
        </div>

    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">

                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch">
                        <form class="form-horizontal example">



                            <div class="row mb-4">
                                <label for="first_name" class="col-md-2 form-label">شماره کاربر</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="first_name" wire:model="search.user_id"
                                           placeholder="شماره کاربر" type="text">
                                </div>
                            </div>



                            <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled" wire:click="updateOrCreate">جست و
                                جو
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">نام کاربر</th>
                                <th scope="col">کاربر دعوت شده</th>
                                <th scope="col">سود حاصله</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($inviteFriends->isNotEmpty())
                                @foreach ($inviteFriends as $inviteFriend)
                                    <tr wire:key="inviteFriend_{{ $inviteFriend->id }}">
                                        <td>{{$inviteFriend->id}}</td>
                                        @if($inviteFriend->user)
                                            <td> <a href="{{ route('admin.user.document', $inviteFriend->user) }}"> {{ $inviteFriend->user->full_name }} - [{{ $inviteFriend->user->id }}] </a></td>
                                        @else
                                            <td><span class="badge rounded-pill bg-danger my-1">کاربر حذف شده</span></td>
                                        @endif
                                        @if($inviteFriend->userInvited)
                                            <td>  <a href="{{ route('admin.user.document', $inviteFriend->userInvited) }}">{{ $inviteFriend->userInvited->full_name ?? '-' }} [{{$inviteFriend->userInvited->id}}] </a></td>
                                        @else
                                            <td><span class="badge rounded-pill bg-danger my-1">کاربر حذف شده</span></td>
                                        @endif
                                        <td>{{number_format($inviteFriend->benefit)}} ریال</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ موردی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $inviteFriends->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>

    <script>
        var myCollapsible = document.getElementById('advanceSearch')
        myCollapsible.addEventListener('show.bs.collapse', function () {
            @this.set('searchPanel', 'show')
            ;
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            @this.set('searchPanel', '')
            ;
        })
    </script>
@endpush
