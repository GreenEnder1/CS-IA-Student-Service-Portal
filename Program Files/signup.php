<?php
$signuperr = "";
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    #Storing form data
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];
    $password_valid = $_POST['password-validation'];
    #Server-Side Info Validation
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) 
    {
        $signuperr = "Valid Alcuin email is required";
    }
    elseif (empty($fname)) 
    {
        $signuperr = "First Name is required";
    }
    elseif (empty($lname)) 
    {
        $signuperr = "Last Name is required";
    }
    elseif (strlen($password) < 8)
    {
        $signuperr = "Password must be at least 8 characters long";
    }
    elseif(!preg_match("/[a-z]/i", $password))
    {
        $signuperr = "Password must contain at least one letter";
    }
    elseif(!preg_match("/[0-9]/", $password))
    {
        $signuperr = "Password must contain at least one number";
    }
    elseif($password !== $password_valid)
    {
        $signuperr = "Passwords must match";
    }
    else
    {
        #Hashing Password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        #Connect to SQL Database
        $mysqli = mysqli_connect("localhost", "root", "", "alcuin_service_db");
        #Check Database for Account
        $sqlvalid = "SELECT * FROM accounts
                WHERE email = '$email'";
        $result = mysqli_query($mysqli, $sqlvalid);
        $acc_data = mysqli_fetch_assoc($result);
        if($acc_data)
        {
            $signuperr = "Account already exists. <a href=\"login.php\" style=\"font-size: 10; margin-top: 60px;\">Try Logging In</a>";
            mysqli_close($mysqli);
        }
        else
        {
            #Add Account to Database
            $sql = "INSERT INTO accounts (email, first_name, last_name, password_hash) VALUES ('$email', '$fname', '$lname', '$password_hash')";
            mysqli_query($mysqli, $sql);
            header("Location: login.php?signup=true");
            mysqli_close($mysqli);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="format.css" />
        <title>AS Service Sign-Up</title>
    </head>
    <header>
        <h1 id="title" style="text-align:center;">Sign-Up for the Alcuin Student Service Portal</h1>
    </header>
    <body>
        <div id="login-box">
            <h4 style="margin:0px; margin-bottom: 35px;">Sign Up</h4>
            <em style="color:#FF1111;"> <?php echo $signuperr; ?> </em>
            <form method="post">
                <label for="email-validation">Email:</label>
                <br>
                <input type="email" id="email-validation" name="email" size="35px" style="margin-bottom: 15px;">
                <br>
                <label for="first-name">First Name:</label>
                <br>
                <input type="text" id="first-name" name="fname" size="35px" style="margin-bottom: 15px;">
                <br>
                <label for="last-name">Last Name:</label>
                <br>
                <input type="text" id="last-name" name="lname" size="35px" style="margin-bottom: 15px;">
                <br>
                <label for="password">Password:</label>
                <br>
                <input type="password" id="password" name="password" size="35px" style="margin-bottom: 20px;">
                <br>
                <label for="password-validation">Confirm Password:</label>
                <br>
                <input type="password" id="password-validation" name="password-validation" size="35px" style="margin-bottom: 20px;">
                <br>
                <input type="submit" label="Sign-Up" style="margin: 5px;">
            </form>
            <a href="login.php" style="font-size: 10; margin-top: 60px;">Already have an account?</a>
        </div>
    </body>
</html>