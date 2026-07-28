@extends('admin::layouts.app')

@section('content')
     <x-coupon::couponcomponet  can="edit" :coupon="$coupon"  :action="route('admin.coupon.update',['coupon'=> $coupon->id])"/>
@endsection

