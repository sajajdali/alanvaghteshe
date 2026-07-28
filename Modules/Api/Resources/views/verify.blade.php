<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{admin_asset('css/style.css')}}" rel="stylesheet"/>

    <title>الان وقتشه - تراکنش</title>
</head>
<body>
<div class="section section__contactUsIntro">
    <div class="section-wrap">
        <div class="main-container rtl container-fluid" style="direction: rtl">
            <div class="col-xl-12 col-md-12">
                <div class="alert alert-success" style="text-align: right" role="alert"> <span
                        class="alert-inner--text">پرداخت شما با موفقیت انجام شد</span>
                </div>

                <div class="alert alert-danger" style="text-align: right" role="alert"> <span
                        class="alert-inner--text">پرداخت شما با موفقیت انجام نشد</span>
                </div>

                <div class="card cart">
                    <div class="card-header border-bottom"><h3 class="card-title">وضعیت سفارش شما</h3></div>
                    <div class="card-body">
                        <div class="d-md-flex">
                            <div class="d-flex">
                                <div class="ms-3 mt-2"><h4 class="mb-1 fw-semibold fs-14">برنامه ورزشی ۱
                                        ماهه</h4>

                                </div>
                            </div>
                            <div class="ms-auto my-auto"><span
                                    class="me-4 my-auto fs-16 fw-semibold">۲،۳۶۰،۰۰۰ تومان</span></div>
                        </div>
                        <div class="d-md-flex mt-5">
                            <div class="d-flex">
                                <div class="ms-3 mt-2"><h4 class="mb-1 fw-semibold fs-14">برنامه رژیم ۱ ماهه</h4>

                                </div>
                            </div>
                            <div class="ms-auto my-auto"><span
                                    class="me-4 my-auto fs-16 fw-semibold">۲،۳۶۰،۰۰۰ تومان</span></div>
                        </div>
                        <table class="table mt-5">
                            <tbody>
                            <tr>
                                <td class="border-top-0">شماره تراکنش</td>
                                <td class="text-end border-top-0">SiS45s</td>
                            </tr>
                            <tr>
                                <td class="border-top-0">مبلغ کل</td>
                                <td class="text-end border-top-0">۳۶۰،۰۰۰ تورمان</td>
                            </tr>

                            <tr>
                                <td class="border-top-0">تخفیف</td>
                                <td class="text-end border-top-0">۲۵،۰۰۰ تومان</td>
                            </tr>

                            <tr>
                                <td class="fs-20 border-top-0">مبلغ پرداختی</td>
                                <td class="text-end fs-20 border-top-0">۳۶۵،۰۰۰ تومان</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-end"><a href="#" class="btn btn-primary">بازگشت به برنامه</a></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JQUERY JS -->
<script src="{{admin_asset('plugins/jquery/jquery.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $("#form_submit").submit();
    });
</script>
</body>
</html>
