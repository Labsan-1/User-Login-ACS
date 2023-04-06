<?php
session_start();

if (! isset ($_SESSION["login_attempt"])) {
    $_SESSION["login_attempt"] = 0;  
}
if (isset($_SESSION["locked"])) {
    $difference = time() - $_SESSION["locked"];
    if ($difference > 5) {
        unset($_SESSION["locked"]);
        unset($_SESSION["login_attempt"]);
    }
}
require "Authenticator.php";


$Authenticator = new Authenticator();
if (!isset($_SESSION['auth_secret'])) {
    $secret = $Authenticator->generateRandomSecret();
    $_SESSION['auth_secret'] = $secret;
}


$qrCodeUrl = $Authenticator->getQR('Advance Cyber Security', $_SESSION['auth_secret']);



if (!isset($_SESSION['failed'])) {
    $_SESSION['failed'] = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Second layer advance security</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <meta name="description" content="Implement Google like Time-Based Authentication into your existing PHP application. And learn How to Build it? How it Works? and Why is it Necessary these days."/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    <link rel='shortcut icon' href='/favicon.ico'  />
    <style>
        body,html {
            height: 100%;
            background:#f1f1f2;
        }       


        .bg { 
            

            /* Full height */
            height: 100%; 
            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat b9d1dc;
           
            background-size: cover;
        }
    </style>
</head>
<body  class="bg">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3"  style="background: #dcdcdc ; padding: 20px; box-shadow: 5px 5px 5px #888888; margin-top: 50px; border-radius:0px">
                <h1 style= "text-align :center";> Two-Way verification Authentication</h1>
                <p style="font-style: normal; text-align :center" > Second layer of Security For Login page</p>
                <hr>
                <form action="check.php" method="post">
                    <div style="text-align: center;">
                        <?php if ($_SESSION['failed']): 
                              $_SESSION["login_attempt"] += 1;?>
                            
                            <div class="alert alert-danger" role="alert">
                                
                                        <strong>!!</strong> Invalid Code.
                            </div>
                            <?php   
                                $_SESSION['failed'] = false;
                            ?>
                        <?php endif ?>
                            
                            <img style="text-align: center;;" class="img-fluid" src="<?php   echo $qrCodeUrl ?>" alt="Verify this Google Authenticator"><br><br>  
                            <p style="font-style: bold; text-align :center;   text-align: justify;" >Kindly download and set up the<strong> Google Authenticator</strong> Google Authenticator application on your mobile device.
                             Once done, launch the app and use it to scan the QR code provided above. After that, enter the code displayed in the app to proceed with the login process.</p>      
                            <input type="text" class="form-control" name="code" placeholder="******" style="font-size: xx-large;width: 200px;border-radius:10 px;text-align: center;display: inline;color: #0275d8;"><br> <br> 
                            <?php
                                if ($_SESSION["login_attempt"] > 1) {
                                    $_SESSION["locked"] = time();
                                    echo "<p style='color:red;'><strong>We regret to inform you that your account has been blocked for 1 hour due to multiple incorrect attempts to enter the two-way verification code.</strong></p>";
                                } else {
                            ?>
                            <button type="submit" class="btn btn-md btn-basic" style="width: 200px;border-radius: 15px;">Verify</button>
                            <?php } ?>
                    </div>

                </form>
            </div>
        </div>
    </div>
    
</body>
</html>

<?php
 
