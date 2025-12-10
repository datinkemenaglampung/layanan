<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Admin</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="assets/modules/izitoast/css/iziToast.min.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <!-- Start GA -->
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img src="assets/img/stisla-fill.svg" alt="logo" width="100" class="shadow-light rounded-circle">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                            </div>

                            <div class="card-body">
                                <form method="POST" id="formLogin" action="{{route('login')}}" class="needs-validation" novalidate="">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                                        <div class="invalid-feedback">
                                            silakan isi Email Anda
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                        </div>
                                        <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                        <div class="invalid-feedback">
                                            silakan isi kata sandi Anda
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                            <label class="custom-control-label" for="remember-me">Remember Me</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                            Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="assets/modules/jquery.min.js"></script>
    <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/stisla.js"></script>

    <!-- JS Libraies -->
    <script src="assets/modules/izitoast/js/iziToast.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="assets/js/page/modules-toastr.js"></script>

    <!-- Template JS File -->
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        $(document).ready(function() {
            $("#formLogin").submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let btnSubmit = form.find("[type='submit']");
                let btnSubmitHtml = btnSubmit.html();
                let url = form.attr("action");
                let data = new FormData(this);
                $.ajax({
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: "POST",
                    url: url,
                    data: data,
                    beforeSend: function() {
                        btnSubmit.addClass("disabled").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop("disabled", "disabled");
                    },
                    success: function(response) {
                        btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                        if (response.status === "success") {
                            iziToast.success({
                                title: 'Success',
                                message: response.message,
                                position: 'topRight'
                            });
                            setTimeout(function() {
                                location.href = response.redirect;
                            }, 1000);
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function(response) {
                        btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    }
                });
            });
        })
    </script>
</body>

</html>