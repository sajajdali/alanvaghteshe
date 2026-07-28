<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{admin_asset('css/style.css')}}" rel="stylesheet"/>

    <title>payment redirect</title>
</head>
<body>
<div class="section section__contactUsIntro">
    <div class="section-wrap">
        <div class="container">
            <h3 style="direction : rtl" class="contactUs-title text-center mb-4">
                <i class="icon-magazine-article turn"></i>
                در حال انتقال به بانک
                ....
            </h3>

            <form action="https://api.paystar.shop/api/pardakht/payment" id="form_submit" class="forms-sample">
                @csrf
                @method('GET')
                <div class="form-group">
                    <input name="amount" type="hidden" value="{{number_format($data['amount'])}}"
                           class="form-control" id="exampleInputUsername1" disabled>
                </div>
                <div class="form-group">
                    <input name="ref_num" type="hidden" value="{{ $data['ref_num'] }}"
                           class="form-control" id="exampleInputEmail1" disabled>
                </div>
                <input name="token" type="hidden" value="{{ $data['token'] }}">
                <button style="visibility: hidden;" type="submit" class="btn btn-success ml-2"> تایید و پرداخت</button>

            </form>
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
