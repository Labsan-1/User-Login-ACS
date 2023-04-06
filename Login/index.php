<?php
    session_start();
    if (!isset($_SESSION["login_attempts"])) {
        $_SESSION["login_attempts"] = 0;  
    }
    if (isset($_SESSION["locked"])) {
        $difference = time() - $_SESSION["locked"];
        if ($difference > 5) {
            unset($_SESSION["locked"]);
            unset($_SESSION["login_attempts"]);
        }
    }

    if (isset($_SESSION['SESSION_EMAIL'])) {
        header("Location: 2way.php");
        die();
    }

    include 'config.php';
    $msg = "";

    if (isset($_GET['verification'])) {
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$_GET['verification']}'")) > 0) {
            $query = mysqli_query($conn, "UPDATE users SET code='' WHERE code='{$_GET['verification']}'");
            
            if ($query) {
                $msg = "<div class='alert alert-success'> Your User account verification has been successfully completed.</div>";
            }
        } else {
            header("Location: index.php");
        }
    }

    if (isset($_POST['submit'])) {
        if(empty($_POST['email']) || empty($_POST['password'])) {
            $msg = "<div class='alert alert-danger'>Please enter both email and password.</div>";
          } else {
        //Protection of SQLIA by escape special characters in a string.
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));
        // Sanitize form (XSS) using htmlspecialchars
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');


        $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if (empty($row['code'])) {
                $_SESSION['SESSION_EMAIL'] = $email;
                header("Location: 2way.php");

            } else {
                $msg = "<div class='alert alert-info'>Please First verify your account and try again.</div>";
            }
        } else {
            $_SESSION["login_attempts"] += 1;
            if ($_SESSION["login_attempts"] == 1) {
                $msg = "<div class='alert alert-danger'> Your email or password do not match. You have 2 attempts remaining.</div>";
            } else if ($_SESSION["login_attempts"] == 2) {
                $msg = "<div class='alert alert-danger'> This is a final and urgent notice: Your login credentials do not match our records. You have one more attempt to enter the correct email or password.</div>";
            } else {
                $to = $email;
                $subject = "Unusual Login Attempt";
                $message = "There have been unusual login attempts on your account. If this was not you, please contact us immediately at zerefghimire1@gmail.com.";
                $headers = "From: labsanghimire22@gmail.com" . "\r\n" .
                           "Reply-To: example@example.com" . "\r\n" .
                           "X-Mailer: PHP/" . phpversion();

                //Code to send email using php mailer
                require 'vendor/autoload.php';
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'labsanghimire22@gmail.com'; //Enter your Gmail username
                $mail->Password = 'ckhrudapmvhdcaso'; //Enter your Gmail password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('labsanghimire22@gmail.com', 'Advance Cyber Security'); //Enter your sender name and email
                $mail->addAddress($to); //Recipient email address
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->send();

                $_SESSION["locked"] = time();
                $msg = "<div class='alert alert-danger'>Your account has been locked due to multiple failed login attempts. Please try again in 5 minutes or contact us for assistance.</div>";
            }
            }
        }
    
    }
?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Login Form Advance Security</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords"
        content="Login Form" />
    <!-- //Meta tag Keywords -->

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!--/Style-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <!--//Style-CSS -->

    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>

</head>

<body>

    <!-- form section start -->
    <section class="w3l-mockup-form">
        <div class="container">
            <!-- /form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="alert-">
                        <span class="fa fa-"></span>
                    </div>
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="images\img2.png " style="max-width: 100%" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Login Now</h2>
                        <p>Please fill following form to login </p>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="email" class="email" name="email" placeholder="Enter Your Email" >
                            <input type="password" class="password" name="password" placeholder="Enter Your Password" style="margin-bottom: 2px;" >
                            <p><a href="forgot-password.php" style="margin-bottom: 15px; display: block; text-align: right;">Forgot Password?</a></p>
                            <?php
                                if ($_SESSION["login_attempts"] > 2) {
                                    $_SESSION["locked"] = time();
                                    echo "<div class='alert alert-danger'> Your logout button has been temporarily disabled for 5 minutes due to multiple rapid login attempts. </div>";
                                    
                                } else {
                            ?>
                            <button name="submit" name="submit" class="btn" type="submit">Login</button>
                            <?php } ?>
                        </form>
                        <div class="social-icons">
                            <p>First Create Your Account! <a href="register.php">Register</a>.</p>
                             
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->

    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function (c) {
            $('.alert-close').on('click', function (c) {
                $('.main-mockup').fadeOut('slow', function (c) {
                    $('.main-mockup').remove();
                });
            });
        });
    </script>

</body>

</html>