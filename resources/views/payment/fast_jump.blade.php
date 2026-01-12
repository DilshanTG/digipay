<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
</head>
<body onload="document.getElementById('payhereForm').submit();">
    <form id="payhereForm" method="POST" action="{{ $data['payhere_url'] }}">
        @foreach($data as $key => $value)
            @if($key !== 'payhere_url')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <noscript>
            <p>Processing payment... If you are not redirected, click below:</p>
            <button type="submit">Proceed to Payment</button>
        </noscript>
    </form>
</body>
</html>
