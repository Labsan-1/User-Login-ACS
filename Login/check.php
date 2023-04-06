<?php
session_start();

require "Authenticator.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("location: 2way.php");
    die();
}

$Authenticator = new Authenticator();

$checkResult = $Authenticator->verifyCode($_SESSION['auth_secret'], $_POST['code'], 2);    // 2 = 2*30sec clock tolerance

if (!$checkResult) {
    $_SESSION['failed'] = true;
    header("location: 2way.php");
    die();
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    <link rel='shortcut icon' href='/favicon.ico'  />

    <style>
        body,html {
            height: 100%;
            background:#f1f1f2;
        }       


        .bg { 
            /* The image used */
           
            /* Full height */
            height: 100%; 
            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
           
            background-size: cover;
        }
    </style>
</head>
<body  class="bg">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3"  style="background: #dcdcdc; padding: 20px; box-shadow: 10px 10px 5px #888888; margin-top: 100px;">
                <hr>
                <div style="text-align: center;">
                    <h1>Welcome To  Secure Site</h1>
                    <p>Thanks for using our security system</p>
                    <div class="circular--landscape"> 
                        <img src="images/high.png" /> 
                    </div>

                    <?php
                    if (!isset($_SESSION['SESSION_EMAIL'])) {
                        header("Location: 2way.php");
                        die();
                    }

                    include 'config.php';

                    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='{$_SESSION['SESSION_EMAIL']}'");
                    if (mysqli_num_rows($query) > 0) {
                        $row = mysqli_fetch_assoc($query);
                                         
                        echo "<div style='margin-bottom: 20px;'>Hello&nbsp;-&nbsp;<strong style='font-size: 20px;'>" . $row['email'] . "</strong></div>";
                        echo "<form action='logout.php'><button type='submit' class='btn btn-danger'>Logout</button></form>";
                        
                        
                    }
                    
                    ?>
                </div>
                <hr>    

                <div style="text-align: center;">
                    <h2> Safe and Secure</h2>
                    <?php
                        // Display user profile information
                    ?>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>
