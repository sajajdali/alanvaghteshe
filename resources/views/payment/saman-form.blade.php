<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>در حال انتقال به بانک...</title>
</head>
<body onload="document.forms['samanForm'].submit()">
<form id="samanForm" name="samanForm" method="POST" action="https://sep.shaparak.ir/OnlinePG/OnlinePG">
    <input type="hidden" name="Token" value="{{ $token }}">
    {{-- GetMethod می‌تونی true, false یا خالی بذاری. پیش‌فرض بهتره خالی باشه --}}
    <input type="hidden" name="GetMethod" value="">
</form>
<p>در حال انتقال به بانک، لطفاً منتظر بمانید...</p>
</body>
</html>
