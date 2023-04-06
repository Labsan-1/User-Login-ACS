
<?php
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    session_start();
    if (isset($_SESSION['SESSION_EMAIL'])) {
        header("Location: welcome.php");
        die();
    }

    //Load Composer's autoloader
    require 'vendor/autoload.php';

    include 'config.php';
    $msg = "";
    $nameErr ="";
    $emailErr ="";
    $passwordErr ="";
    $confirmPasswordErr = "";
  
    // Password dictionary
    $dictionary = file('dictionary.txt', FILE_IGNORE_NEW_LINES);

    if (isset($_POST['submit'])) { { 
    
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);
        $code = mysqli_real_escape_string($conn, md5(rand())); 
           // Sanitize $email and $password using htmlspecialchars
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
        $confirm_password = htmlspecialchars($confirm_password, ENT_QUOTES, 'UTF-8');
        $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    

        // Name Validation   
        if ($name === "") {  
        $nameErr = "Your Username Can't Be Empty*";  
        } else if (!preg_match('/^[a-zA-Z ]*$/', $name)) {   
        $nameErr = "UserName canot have extra Character";  
        }
        //Email Validation   
        if ($email === "") {  
            $emailErr = "Your Email Cant Be Empty*";  
        } else {  
            // check that the e-mail address is well-formed  
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
                $emailErr = " Email is missing @ format";  
            }  
        }

       // Password Validation   
        if ($password === "") {  
        $passwordErr = "Your Password Can't Be Empty*";  
        }
        else if (in_array($password, $dictionary)) {
            $passwordErr = "Your password is too common. Please choose a different password*";
        } else if (strpos($password, $name) !== false) {  
            $passwordErr = "Your password contains your username. Please choose a different password*";  
        } else if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $password)){
            $passwordErr = "Password is missing password criteria format";  
        }


        //Confirm Password Validation   
        if ($confirm_password === "") {  
            $confirmPasswordErr = "Your Confirm-Password Cant Be Empty*";  

        }else if ($password != $confirm_password) {  
            $confirmPasswordErr = "Password and Confirm Password do not match*";  

        }  
         

        if(!$emailErr && !$nameErr && !$passwordErr && !$confirmPasswordErr) {
                // Verify reCAPTCHA response
                  $secretKey = '6Lf4fi4lAAAAAAjqMP9zOJwMJurX7E-AuLJwygbt';
                    $response = $_POST['g-recaptcha-response'];
                    $remoteIp = $_SERVER['REMOTE_ADDR'];
                 $url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp");
                $result = json_decode($url, TRUE);

                if ($result['success'] != 1) {
                    $msg = "<div class='alert alert-danger'>reCAPTCHA verification failed. Please try again.</div>";
                } else {
                    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
                        $msg = "<div class='alert alert-danger'>{$email} - This email address has been already registered.</div>";
                    } else {
                        if ($password === $confirm_password) {
                            // Hash the password 
                            $hashed_password = md5($password);
                                
                            $sql = "INSERT INTO users (name, email, password, code) VALUES ('{$name}', '{$email}', '{$hashed_password}', '{$code}')";
                            $result = mysqli_query($conn, $sql);
                

                    if ($result) {
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
                            $mail->Subject = 'Advance Cyber Security';
                            $mail->Body    = 'Here is the login verification link <b><a href="http://localhost/login/?verification='.$code.'">http://localhost/login/?verification='.$code.'</a></b>';
    
                            $mail->send();
                            echo 'Message has been sent';
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                        echo "</div>";
                        $msg = "<div class='alert alert-info'>We've send a verification link on your email address.</div>";
                    } else {
                        $msg = "<div class='alert alert-danger'>Something wrong went.</div>";
                    }
                } else {
                    $msg = " ";
                }
            }
                  

            }      
        }
    }
}
    

?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Login Form Advance Security</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
    <section class="w3l-mockup-form" >     
        <div class="container" >
            <!-- /form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="alert-">
                        
                        <span class="fa fa-"></span>
                    </div>
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="images/img1.png" alt="">
                          
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Register Now</h2>
                        <p> Please fill all the necesarry credentials to login sucessfuly. </p>
                        <?php echo $msg; ?>
                        <form action="" method="post" onsubmit="return submitUserForm();" >
                            <input type="text" class="name" id="name" name="name" placeholder="Enter Your Name" value="<?php if (isset($_POST['submit'])) { echo $name; } ?>" >
                            <span id="namemsg" style="color: red; font-size: 13px;"><?php echo $nameErr; ?></span>
                            <input type="email" class="email" id="email" name="email" placeholder="Enter Your Email" value="<?php if (isset($_POST['submit'])) { echo $email; } ?>">
                            <span id="emailmsg" style="color: red; font-size: 13px;"><?php echo $emailErr; ?></span>
                            <input type="password" class="password" id="pwd" name="password" placeholder="Enter Your Password">
                            <span id="pwdmsg"style="color: red; font-size: 13px;"><?php echo $passwordErr; ?></span>
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
                            <input type="password" class="confirm-password" id="cnfpwd" name="confirm-password" placeholder="Enter Your Confirm Password" >
                            <span id="cnfpwdmsg"style="color: red; font-size: 13px;"><?php echo $confirmPasswordErr; ?></span>
                            <!-- Google Rechaptcha Site key -->
                            <br>
                            <div class="g-recaptcha" data-sitekey="6Lf4fi4lAAAAAEvKMKB4ycHWAcbj5joX2K2fVpqy"></div>
                            </br>
                            <button name="submit" class="btn btn-default hidden" type="submit" id="submit">Register</button>
                        </form>
                        <div class="social-icons">
                            <p>Have an account! <a href="index.php">Login</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->

    <script src="js/jquery.min.js"></script>
    <script src="js/pasword.js"></script>

    <script>
      

</body>

</html>