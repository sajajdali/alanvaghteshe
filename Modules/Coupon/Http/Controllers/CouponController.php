<?php

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coupon\Entities\Coupon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Contracts\Support\Renderable;
use Modules\Coupon\Http\Requests\CouponRequest;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $coupons = Coupon::orderByDesc('id')->paginate(20) ;
        return view('coupon::index',compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        return view('coupon::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CouponRequest $request)
    {
        $this->UpdateOrCreateAction($request);
        $request->session()->flash('successful', 'کوپون با موفقیت اضافه شد');
        return redirect()->route('admin.coupon.index');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('coupon::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Coupon $coupon)
    {
     
        $start = verta($coupon->start_at) ;
        return view('coupon::edit',compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CouponRequest $request,Coupon $coupon)
    {
        // dd($coupon);
        $this->UpdateOrCreateAction($request,$coupon->id);
        $request->session()->flash('successfully', 'کوپون با موفقیت اضافه شد');
        return redirect()->route('admin.coupon.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    private function UpdateOrCreateAction(Request $request , $id = null)
    {
        $detail = [];
        if ($request->get('start_at')) {
            $startAt = Verta::parse( $request->get('start_at'))->formatGregorian("Y-m-d" );
        }
        if ($request->get('end_at')) {
            $endAt = Verta::parse( $request->get('end_at'))->formatGregorian("Y-m-d" );
        }
        $title = $request->get('title');
        $code = $request->get('code');
        $value = $request->get('value');
        $is_percent = $request->get('is_percent');
        $minimum_spend = $request->get('minimum_spend');
        $maximum_spend = $request->get('maximum_spend');
        $usable_count = $request->get('usable_count');
        $can_used_for	= $request->get('can_used_for');
        $active = $request->get('active') == 'on';
        $dataUpdate = [
            'title'          =>  $title,
            'code'           =>  $code,
            'value'          =>  $value,
            'is_percent'     =>  $is_percent,
            'minimum_spend'  =>  $minimum_spend,
            'maximum_spend'  =>  $maximum_spend,
            'usable_count'   =>  $usable_count,
            'can_used_for'   =>  $can_used_for,
            'start_at'       =>  $startAt,
            'end_at'         =>  $endAt ?? '',
            'active'         =>  $active ?? '',
        ];
        if ($id == null){
            Coupon::create($dataUpdate);
        } else {
            Coupon::findOrFail($id)->update($dataUpdate);
        }
        }
}
