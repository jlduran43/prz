<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Ingreso | PRZ</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >

    <style>
        body {
            background:
                linear-gradient(
                    rgba(244, 248, 244, 0.92),
                    rgba(244, 248, 244, 0.92)
                ),
                #f4f6f9;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 15px;
        }

        .card {
            border: none;
            border-top: 4px solid #28a745;
            border-radius: 8px;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            padding: 30px 20px 15px;
        }

        .login-icon {
            width: 80px;
            height: 80px;

            margin: 0 auto 15px;

            border-radius: 50%;

            background: #28a745;

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 36px;

            box-shadow: 0 4px 10px
                rgba(40, 167, 69, 0.25);
        }

        .login-header h2 {
            color: #218838;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #6c757d;
            margin-bottom: 0;
        }

        .form-control:focus {
            border-color: #28a745;

            box-shadow:
                0 0 0 0.2rem
                rgba(40, 167, 69, 0.15);
        }

        .input-group-text {
            background: #f8f9fa;
            color: #28a745;
        }

        .btn-parque {
            background: #28a745;
            border-color: #28a745;
            color: white;
            font-weight: 600;
        }

        .btn-parque:hover {
            background: #218838;
            border-color: #1e7e34;
            color: white;
        }

        .login-footer {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            margin-top: 20px;
        }

        .login-footer i {
            color: #28a745;
        }
    </style>

</head>

<body>

<div class="login-card">

    <div class="card shadow">

        <div class="login-header">

            <div class="login-icon">
                <i class="fas fa-tree"></i>
            </div>

            <h2>PRZ</h2>

            <p>
                Parque Pedro del Río Zañartu
            </p>

        </div>

        <div class="card-body px-4 pb-4">

            <h5 class="text-center mb-4">
                Acceso al sistema de reservas
            </h5>

            @if (session('success'))

                <div class="alert alert-success">

                    <i class="fas fa-check-circle mr-1"></i>

                    {{ session('success') }}

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('login.attempt') }}"
            >

                @csrf


                {{-- Correo --}}
                <div class="form-group">

                    <label for="email">
                        Correo electrónico
                    </label>

                    <div class="input-group">

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control
                                @error('email')
                                    is-invalid
                                @enderror"
                            value="{{ old('email') }}"
                            placeholder="correo@prz.cl"
                            autocomplete="email"
                            autofocus
                        >

                        <div class="input-group-append">

                            <div class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </div>

                        </div>

                        @error('email')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>


                {{-- Contraseña --}}
                <div class="form-group">

                    <label for="password">
                        Contraseña
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control
                                @error('password')
                                    is-invalid
                                @enderror"
                            placeholder="Ingrese su contraseña"
                            autocomplete="current-password"
                        >

                        <div class="input-group-append">

                            <div class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </div>

                        </div>

                        @error('password')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>


                {{-- Recordarme --}}
                <div class="form-group">

                    <div class="custom-control custom-checkbox">

                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="remember"
                            name="remember"
                            value="1"
                        >

                        <label
                            class="custom-control-label"
                            for="remember"
                        >
                            Recordarme
                        </label>

                    </div>

                </div>


                {{-- Botón --}}
                <button
                    type="submit"
                    class="btn btn-parque btn-block py-2"
                >

                    <i class="fas fa-sign-in-alt mr-1"></i>

                    Ingresar al sistema

                </button>

            </form>


            <div class="login-footer">

                <i class="fas fa-leaf mr-1"></i>

                Sistema de Reservas PRZ

            </div>

        </div>

    </div>

</div>

</body>

</html>