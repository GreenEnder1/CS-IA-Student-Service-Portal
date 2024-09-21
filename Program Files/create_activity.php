<?php
    session_start();
    if(isset($_SESSION["user_id"]))
    {
        $mysqli = mysqli_connect("localhost", "root", "", "alcuin_service_db");
        $sql = "SELECT * FROM accounts
                WHERE account_id = {$_SESSION["user_id"]}";
        $result = mysqli_query($mysqli, $sql);
        $acc_data = mysqli_fetch_assoc($result);
    }
    else
    {
        header("Location: login.php");
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST")
    {
        $name = $_POST['activity_name'];
        $desc = $_POST['activity_description'];
        $start_dt = $_POST['start_date'];
        $end_dt = $_POST['end_date'];
        $service_hrs = $_POST['service_hours'];
        #Connect to SQL Database
        $mysqli = mysqli_connect("localhost", "root", "", "alcuin_service_db");
        $sql = "INSERT INTO service_activities (organizer_id, name, description, hours, start_date, end_date) VALUES ('$_SESSION[user_id]', '$name', '$desc', '$service_hrs', '$start_dt', '$end_dt')";
        mysqli_query($mysqli, $sql);
        header("Location: main.php?created=true");
        mysqli_close($mysqli);
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="format.css">
        <title>AS Student Service Portal</title>
    </head>
    <header>
        <a href="main.php"><img src="./img/home-button.svg" height="40px"></a>
        <h1 id="title" style="margin:auto; margin-left: 23%; width:50%; text-align:center; display:inline-block;">Alcuin Student Service Portal</h1>
        <div id="acc_info" style="float:right; margin-right: 5px;">
        <p style="margin-top: 0px; margin-bottom: 0px; text-align:right;">Logged in as: <?php echo $acc_data["first_name"]; ?> <?php echo $acc_data["last_name"]; ?></p>
        <a style="float:right;"href="main.php?logout=true">Log Out</a>
        </div>
    </header>
    <body>
        <br>
        <h2 id="experience-name" style="margin:auto; width:50%; text-align:center;">Create Experience</h2>
        <br>
        <div id="activity-box">
            <form method="post">
                <label for="activity_name">Activity Name:</label>
                <br>
                <input type="text" id="activity_name" name="activity_name" size="35px" style="margin-bottom: 15px;" required>
                <br>
                <label for="activity_description">Activity Description (include details not listed elsewhere):</label>
                <br>
                <textarea rows="5" cols="40" name="activity_description" style="margin-bottom: 15px;" required></textarea>
                <br>
                <label for="start_date">Start Date & Time:</label>
                <br>
                <input type="datetime-local" id="start_date" name="start_date" style="margin-bottom: 15px;" required>
                <br>
                <label for="end_date">End Date & Time:</label>
                <br>
                <input type="datetime-local" id="end_date" name="end_date" style="margin-bottom: 15px;" required>
                <br>
                <label for="service_hours">Total Service Hours:</label>
                <br>
                <input type="number" min="0" id="service_hours" name="service_hours" style="margin-bottom: 15px;" required>
                <br>
                <input type="submit" value="Create Experience" style="margin:5px;">
            </form>
        </div>
    </body>