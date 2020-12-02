<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="cache-control" content="max-age=31536000" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="Sistem Absensi Face" />
    <meta name="robots" content="index,follow" />
    <meta name="google" content="sitelinkssearchbox" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title>Admin Digimon-P</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{url('AdminLTE/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ url ('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url ('AdminLTE/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ url ('AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ url ('AdminLTE/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ url ('AdminLTE/plugins/summernote/summernote-bs4.css') }}">


    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ url ('css/style.css') }}">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- <link rel="shortcut icon" href="http://webiot.ujpbsla.com/assets/absensi/img/favicon.ico" /> -->
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card">
            <div class="login-logo">
                <a href="../../index2.html">
                    <img src="{{ url('images/logoIP.png') }}" alt="Logo Indonesia Power">
                    <span>
                        PLTU BANTEN 1 SURALAYA OMU
                    </span>
                </a>
            </div>

            <!-- Form Forgot -->
            <div class="card-body login-card-body" id="form-forget">
                <p class="login-box-msg">Masukkan email anda untuk mereset password</p>
                <form action="http://webiot.ujpbsla.com/absensi/sessions/forget" method="post">

                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="reset" class="btn btn-outline-danger btn-block link-back">Kembali</button>
                        </div>
                        <div class="col-4"></div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Kirim</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>

            <!-- Form Login -->
            <div class="card-body login-card-body" id="form-login">
                <p class="login-box-msg">Masuk ke akun anda</p>
                <form method="POST" action="{{ route('login') }}">
                        @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group mb-1">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row row-login mt-3">
                        <!-- /.col -->
                        <div class="col-8">
                            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>


            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="http://webiot.ujpbsla.com/assets/revamp/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="http://webiot.ujpbsla.com/assets/revamp/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="http://webiot.ujpbsla.com/assets/revamp//plugins/select2/js/select2.full.min.js"></script>

    <!-- AdminLTE App -->
    <script src="http://webiot.ujpbsla.com/assets/revamp/dist/js/adminlte.min.js"></script>

    <script>
        const ID_FORM_LOGIN = "#form-login";
        const ID_FORM_FORGET = "#form-forget";
        const ID_FORM_DAFTAR = "#form-daftar";
        const allForm = $(".login-card-body");

        const changePage = function(form_id) {
            allForm.hide();
            $(form_id).show();
        }

        const linkForgot = $("#link-forgot");
        const linkDaftar = $("#link-daftar");
        const linkBack = $(".link-back");

        linkBack.click(e => changePage(ID_FORM_LOGIN))

        linkDaftar.click(e => changePage(ID_FORM_DAFTAR))

        linkForgot.click(e => changePage(ID_FORM_FORGET))

        $(".select2").select2({
            theme: 'bootstrap4',
            placeholder: "Pilih Bagian",
            allowClear: true
        });

        const toggleShowPassword = $("#toggle-show-password");
        const inputPassword = $(".input-password");

        const CLASS_ICON_EYE_OPEN = "fa-eye";
        const CLASS_ICON_EYE_SLASH = "fa-eye-slash";
        toggleShowPassword.click(function(e) {
            const icon = $(this).find("i")[0];
            const isMasked = $(icon).hasClass(CLASS_ICON_EYE_OPEN);
            if (isMasked) {
                $(icon).removeClass(CLASS_ICON_EYE_OPEN);
                $(icon).addClass(CLASS_ICON_EYE_SLASH);
                inputPassword.prop("type", "text");
            } else {
                $(icon).removeClass(CLASS_ICON_EYE_SLASH);
                $(icon).addClass(CLASS_ICON_EYE_OPEN);
                inputPassword.prop("type", "password");
            }
        });
    </script>

</body>

</html>