<?php
include_once("inc/conn.php");
$error = array();

if(isset($_SESSION['user_id'])) header('location:index.php');

if (isset($_POST["username"])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $query = "SELECT * FROM users WHERE `user_name`='$username' AND `user_pass`='$password'";
        $result = $conn->query($query);
        if ($result->num_rows >= 1) {
            $fetch = $result->fetch_assoc();
            $_SESSION['user_id'] = $fetch['user_id'];
            $_SESSION['username'] = $fetch['user_name'];
            header('location:index.php');
        } else {
            array_push($error, array("msg" => "Invalid username or password please try again...", "type" => "danger"));
        }
    } else {
        array_push($error, array("msg" => "Username and password must not be empty.", "type" => "warning"));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="shortcut icon" href="images/map.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/1164050.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: rgba(26, 34, 38, 0.8);
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
            padding: 20px;
        }

        .login-key {
            height: 100px;
            font-size: 80px;
            line-height: 100px;
            background: -webkit-linear-gradient(#27EF9F, #0DB8DE);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-title {
            margin-top: 15px;
            text-align: center;
            font-size: 30px;
            letter-spacing: 2px;
            font-weight: bold;
            color: #ECF0F5;
        }

        .login-form {
            margin-top: 25px;
            text-align: left;
        }

        .form-control {
            background-color: #1A2226;
            border: none;
            border-bottom: 2px solid #0DB8DE;
            border-radius: 0px;
            font-weight: bold;
            outline: 0;
            margin-bottom: 20px;
            padding: 10px;
            color: #ECF0F5;
            width: 100%;
            font-size: 16px;
        }

        .btn-outline-primary {
            border-color: #0DB8DE;
            color: #0DB8DE;
            border-radius: 0px;
            font-weight: bold;
            letter-spacing: 1px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            width: 100%;
            padding: 10px;
            font-size: 18px;
        }

        .btn-outline-primary:hover {
            background-color: #0DB8DE;
            right: 0px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col s12 m8 l6 offset-m2 offset-l3">
                <div class="login-box z-depth-3">
                    <div class="login-key">
                        <i class="fa fa-leaf" aria-hidden="true"></i>
                    </div>
                    <div class="login-title">
                        AgriMapping
                    </div>

                    <div class="login-form">
                        <form method="post" action="" onsubmit="return validateForm()">
                            <div class="input-field">
                                <label for="username" class="form-control-label">USERNAME</label>
                                <input type="text" id="username" class="form-control" name="username" value="<?php echo $_POST['username'] ?? '' ?>">
                                <span class="helper-text" id="username-error" style="color: red; display: none;">Please enter your username</span>
                            </div>
                            <div class="input-field">
                                <label for="password" class="form-control-label">PASSWORD</label>
                                <input type="password" id="password" class="form-control" name="password">
                                <span class="helper-text" id="password-error" style="color: red; display: none;">Please enter your password</span>
                            </div>

                            <div class="loginbttm center-align">
                                <button class="btn icon-right waves-effect waves-light" type="submit" name="action">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const usernameError = document.getElementById('username-error');
            const passwordError = document.getElementById('password-error');

            if (!username) {
                usernameError.style.display = 'block';
                isValid = false;
            } else {
                usernameError.style.display = 'none';
            }

            if (!password) {
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            return isValid;
        }
    </script>
</body>

</html>