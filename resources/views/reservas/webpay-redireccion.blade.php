<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Redirigiendo a Webpay</title>
</head>

<body>

    <p>
        Redirigiendo a Webpay...
    </p>

    <form id="form-webpay" action="{{ $url }}" method="POST">
        <input type="hidden" name="token_ws" value="{{ $token }}">
    </form>

    <script>
        document
            .getElementById('form-webpay')
            .submit();
    </script>

</body>

</html>