<?php

if(!isset($_SESSION)){
    session_start();
}
if(isset( $_SESSION["customer"]["id"])){
    header("Location: customer_profile.php");
    exit();
}

include "../include/db_connect.php";
if (isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        $stmt = $con->prepare("SELECT id, username, password FROM customers WHERE username = ? AND password = ? LIMIT 1");
        $stmt->bind_param("ss", $username,$password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
                
            /*Set session variables*/ 
            $_SESSION["customer"]["id"] = $row["id"];
            $_SESSION["customer"]["username"] = $row["username"];

            /*Log user's IP*/ 
            $loggeIP = $_SERVER['REMOTE_ADDR'];
            $stmt = $con->prepare("INSERT INTO user_login_log(username, time, ip, status) VALUES (?, NOW(), ?, 'Success')");
            $stmt->bind_param("ss", $username, $loggeIP);
            $stmt->execute();


            header("Location: customer_profile.php");
            exit();
           
        } else {
            $wrong_info = "Username or Password is incorrect!";
        }

        /* Log failed login attempt*/
        $stmt = $con->prepare("INSERT INTO user_login_log(username, time, ip, status) VALUES (?, NOW(), ?, 'Failed')");
        $stmt->bind_param("ss", $username, $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
    } else {
        $wrong_info = "Please enter both username and password!";
    }
}


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login | ISP-BILLING SOFTWARE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-clr: #0a58ca;
            --primary-clr-lite: #3d8bfd;
            --secondary-clr: #20c997;
            --secondary-clr-lite: #6ee7b7;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        /*---------------- Animated Gradient Background --------------*/
        .body-wrapper {
            min-height: 100vh;
            background: linear-gradient(
                120deg,
                var(--primary-clr),
                var(--primary-clr-lite),
                var(--secondary-clr),
                var(--secondary-clr-lite)
            );
            background-size: 400% 400%;
            animation: gradient-animation 15s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ---------------- Login Card ----------------- */
        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: none;
        }

        .login-card h4 {
            font-weight: 600;
        }

        /* ----------- Inputs ------------ */
        .form-control {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .form-control:focus {
            border-color: var(--primary-clr-lite);
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25);
        }

        /*------------ Gradient Button -------------*/
        .btn-login {
            background: linear-gradient(
                to right,
                var(--primary-clr),
                var(--secondary-clr)
            );
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 500;
            color: #fff;
            transition: 0.3s;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /*-------------- Footer Link -------------*/
        .forgot-link {
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="body-wrapper">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 col-xl-4">

                <div class="card login-card">
                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <img src="../assets/images/it-fast.png" height="40" alt="logo">
                        </div>

                        <h4 class="text-center mb-2">Welcome Back</h4>
                        <p class="text-muted text-center mb-4">
                            Sign in to continue to ISP Billing System
                        </p>

                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" placeholder="Enter Password" class="form-control" required>
                            </div>

                            <?php if (isset($wrong_info)): ?>
                                <p class="text-danger text-center mb-3">
                                    <?php echo $wrong_info; ?>
                                </p>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <button class="btn btn-login w-100" name="login">
                                Log In
                            </button>

                            <div class="text-center mt-3">
                                <a href="#" class="text-muted small forgot-link">
                                    Forgot password?
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

</body>
</html>
