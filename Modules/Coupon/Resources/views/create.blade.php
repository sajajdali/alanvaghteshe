@extends('admin::layouts.app')

@section('content')
    <x-coupon::couponcomponet can="create" :action="route('admin.coupon.store')" :coupon="null" />
@endsection
