<?php
session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: welcome.php");
    die();
}

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

include 'config.php';
$msg = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
// Email validation
if (empty($email)) {
    $msg = "<div class='alert alert-danger'>Please enter your email.</div>";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $msg = "<div class='alert alert-danger'>Invalid email format.</div>";
} else {
    $userDetail = "SELECT * FROM users WHERE email='{$email}' LIMIT 1";
    $userDetailRun = mysqli_query($conn, $userDetail);
    if(mysqli_num_rows($userDetailRun)>0)
    {
        $code = mysqli_real_escape_string($conn, md5(rand()));
        $row = mysqli_fetch_array($userDetailRun);
        $get_pass_ch_dt = strtotime($row['pass_ch_dt']);
        $pass_ch_exp_dt = strtotime("+7 day",$get_pass_ch_dt);
        $get_dt_now = date('Y-m-d');
        if($get_dt_now >= date('Y-m-d',$pass_ch_exp_dt)) {
            $query = mysqli_query($conn, "UPDATE users SET code='{$code}' WHERE email='{$email}'");
            if ($query) {
                echo "<div style='display: none;'>";
                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                    $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $mail->Username   = 'labsanghimire22@gmail.com';                     //SMTP username
                    $mail->Password   = 'ckhrudapmvhdcaso';                               //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                    //Recipients
                    $mail->setFrom('labsanghimire22@gmail.com');
                    $mail->addAddress($email);

                    //Content
                    $mail->isHTML(true);                                  //Set email format to HTML
                    $mail->Subject = 'Password Change';
                    $mail->Body    = 'Here is the verification link for changing password <b><a href="http://localhost/login/change-password.php?reset='.$code.'">http://localhost/login/change-password.php?reset='.$code.'</a></b>';

                    $mail->send();
                    echo 'Message has been sent';

                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
                echo "</div>";        
                $msg = "<div class='alert alert-info'>We've sent a verification link to your email address.</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>$email - Due to your recent password update, you will only be able to modify your password again after a period of 7 days.</div>"; 
        }
    } else {
        $msg = "<div class='alert alert-danger'>$email - This email address does not exist in our database.</div>";
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
                            <img src="images/img3.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Forgot Password</h2>
                        <p> Fill The Form Below If You Forget Your Password</p>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="email" class="email" name="email" placeholder="Enter Your Email" >
                            <button name="submit" class="btn" type="submit">Send Reset Link</button>
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