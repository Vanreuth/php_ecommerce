<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-container {
            max-width: 500px;
            width: 90%;
            padding: 15px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        .card-body {
            padding: 30px;
        }
        .form-control {
            height: 46px;
            border-radius: 8px;
            border: 1px solid #e1e1e1;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        }
        textarea.form-control {
            height: auto;
            min-height: 100px;
        }
        .btn-primary {
            background: #4e73df;
            border: none;
            height: 46px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #2e59d9;
            transform: translateY(-1px);
        }
        .card-footer {
            background: #fff;
            border-top: 1px solid #eee;
            padding: 20px;
            border-radius: 0 0 10px 10px !important;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header i {
            font-size: 48px;
            color: #4e73df;
            margin-bottom: 10px;
        }
        .register-header h4 {
            color: #333;
            font-weight: 600;
        }
        .input-group-text {
            background: transparent;
            border-right: none;
        }
        .form-control.with-icon {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <div class="register-header">
                    <i class="fas fa-user-plus"></i>
                    <h4>Create Account</h4>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="../controllers/AuthController.php?action=register" method="post">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" class="form-control with-icon" placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control with-icon" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone" class="form-control with-icon" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea name="address" class="form-control with-icon" placeholder="Address" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control with-icon" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="confirm_password" class="form-control with-icon" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </form>
            </div>
            <div class="card-footer text-center">
                <small>Already have an account? <a href="login.php" class="text-primary">Login</a></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 