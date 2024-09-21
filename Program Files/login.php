<?php
$is_invalid = false;
if(isset($_GET['signup']))
{
    $signup = $_GET['signup'];
}
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    #Storing form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    #Connect to SQL Database
    $mysqli = mysqli_connect("localhost", "root", "", "alcuin_service_db");
    $sql = "SELECT * FROM accounts
            WHERE email = '$email'";
    $result = mysqli_query($mysqli, $sql);
    $acc_data = mysqli_fetch_assoc($result);
    if($acc_data && password_verify($password, $acc_data["password_hash"]))
    {
        session_start();
        session_regenerate_id();
        $_SESSION["user_id"]=$acc_data["account_id"];
        mysqli_close($mysqli);
        header("Location: main.php");
        exit;
    }
    $is_invalid = true;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="format.css" />
        <title>AS Student Service Portal</title>
    </head>
    <header>
        <h1 id="title" style="text-align:center;">Log-In to Alcuin Student Service Portal</h1>
    </header>
    <body>
        <div id="login-box">
            <h4 style="margin:0px; margin-bottom: 35px;">Log In</h4>
            <?php if($is_invalid): ?>
                <em style="color:#FF1111;">Invalid Login</em>
            <?php endif; ?>
            <?php if(isset($signup) && !$is_invalid): ?>
                <em style="color:#11FF11;">Sign-Up Successful!</em>
            <?php endif; ?>
            <form method="post">
                <label for="email">Email:</label>
                <br>
                <input type="email" id="email" name="email" size="35px" style="margin-bottom: 15px;">
                <br>
                <label for="password">Password:</label>
                <br>
                <input type="password" id="password" name="password" size="35px" style="margin-bottom: 20px;">
                <br>
                <input type="submit" label="Submit" style="margin: 5px;">
            </form>
            <a href="signup.php" style="font-size: 10; margin-top: 60px;">Dont have an account?</a>
        </div>
    </body>
</html>