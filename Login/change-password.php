<?php
 session_start();
if(isset($_POST['email'])){
    $email = $_POST['email'];
    $mail->addAddress($email); //Recipient email address
}


$msg = "";
include 'config.php';

require 'vendor/autoload.php'; // Include PHPMailer library


function validatePasswordStrength($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);
    
    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    } else {
        return true;
    }
}

if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$_GET['reset']}'")) > 0) {
    $result = mysqli_query($conn, "SELECT email FROM users WHERE code='{$_GET['reset']}'");
    $row = mysqli_fetch_assoc($result);
    $email = $row['email'];

if (isset($_GET['reset'])) {
   

    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$_GET['reset']}'")) > 0) {
        if (isset($_POST['submit'])) {

            $password = mysqli_real_escape_string($conn, md5($_POST['password']));
            $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm-password']));
            $pass_ch_dt = date('Y-m-d');
    
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE password='{$password}'")) > 0) {
                $msg = "<div class='alert alert-danger'> Previous password and New password cannot be same.</div>";
            } else {
                if (!validatePasswordStrength($_POST['password'])) {
                    $msg = "<div class='alert alert-danger'> Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.</div>";
                } elseif ($password !== $confirm_password) {
                    $msg = "<div class='alert alert-danger'>Password and Confirm Password do not match.</div>";
                } else {
                    $query = mysqli_query($conn, "UPDATE users SET password='{$password}', pass_ch_dt = '{$pass_ch_dt}', code='' WHERE code='{$_GET['reset']}'");
    
                    if ($query) {
                        $msg = "<div class='alert alert-warning'>Your password is changed successfully.</div>";
                        
                        // Send email notification
                        $mail = new PHPMailer\PHPMailer\PHPMailer();
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'labsanghimire22@gmail.com'; // Set your Gmail email address
                        $mail->Password = 'ckhrudapmvhdcaso'; // Set your Gmail password
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;
                        $mail->setFrom('labsanghimire22@gmail.com', 'Advance Cyber Security'); // Set the sender's name and email address
                        $mail->addAddress($email); //Recipient email address
                        $mail->Subject = 'Your password is changed  recently'; // Set the email subject
                        $mail->Body = 'Your password has been changed recently. If it was not you, please contact us at zerefghimire1@gmail.com.'; // Set the email body
                        if (!$mail->send()) {
                            $msg .= "<div class='alert alert-danger'>Message could not be sent. Mailer Error: " . $mail->ErrorInfo . "</div>";
                        }
                    }
                }
            }
            }
            
        }
    } else {
        $msg = "<div class='alert alert-danger'>Reset Link do not match.</div>";
    }

} else {
    header("Location: forgot-password.php");
}

?>






<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Login Form Fun Olympic </title>
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
                            <img src="images/image3.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Change Password</h2>
                        <p> Fill the form given below to change password </p>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="password" class="password" name="password" id="pdw" placeholder="Enter Your Password" >
                               <!-- Password Strength Wrap -->
                               <div id="pwd_strength_wrap">
                            <div id="passwordDescription">Password not entered</div>
                            <div id="passwordStrength" class="strength0"></div>
                            <div id="pswd_info">
                            <strong>Strong Password Tips:</strong>
                            <ul>
                                <li class="invalid" id="length">At least 6 characters</li>
                                <li class="invalid" id="pnum">At least one number</li>
                                <li class="invalid" id="capital">At least one lowercase &amp; Uppercase letter</li>
                                <li class="invalid" id="spchar">At least one special character</li>
                            </ul>
                            </div><!-- END pswd_info -->
                            </div>
                            <!-- END pasword strength wrap -->
                            <input type="password" class="confirm-password" name="confirm-password" placeholder="Enter Your Confirm Password" >
                            <button name="submit" class="btn" type="submit">Change Password</button>
                        </form>
                        <div class="social-icons">
                            <p>Back to! <a href="index.php">Login</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->
    <script src="js/pasword.js"></script>

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