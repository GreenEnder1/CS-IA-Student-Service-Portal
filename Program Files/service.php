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
    $valid_id = false;
    $disabled = false;
    $is_organizer = false;
    if(isset($_GET['id']))
    {
        $activity_id = $_GET['id'];
        $sql = "SELECT * FROM service_activities
                INNER JOIN accounts
                ON service_activities.organizer_id = accounts.account_id";
        $result = mysqli_query($mysqli, $sql);
        while($activity_data = mysqli_fetch_assoc($result))
        {
            if($activity_id == $activity_data['activity_id'])
            {
                $valid_id = true;
                if($acc_data['account_id'] == $activity_data['organizer_id'])
                {
                    $is_organizer = true;
                    break;
                }
                $sql = "SELECT account_id FROM activity_participants
                        WHERE activity_participants.activity_id = $activity_id";
                $result = mysqli_query($mysqli, $sql);
                while($participant_id = mysqli_fetch_assoc($result))
                {
                    if($acc_data['account_id'] == $participant_id['account_id'] || strtotime($activity_data['end_date']) < time())
                    {
                        $disabled = true;
                        break;
                    }
                }
                break;
            }
        }
    }
    if(!$valid_id) {header("Location: main.php");}
    if ($_SERVER["REQUEST_METHOD"] === "POST")
    {
        $extra_info = $_POST['extra_info'];
        $mysqli = mysqli_connect("localhost", "root", "", "alcuin_service_db");
        $sql = "INSERT INTO activity_participants (activity_id, account_id, extra_info) VALUES ('$activity_id', '$_SESSION[user_id]', '$extra_info')";
        mysqli_query($mysqli, $sql);
        header("Location: main.php?signup=true");
        mysqli_close($mysqli);
        exit;
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
        <a href="main.php"><img src="./img/home-button.svg" height="40px"></a>
        <h1 id="title" style="margin:auto; margin-left: 23%; width:50%; text-align:center; display:inline-block;">Alcuin Student Service Portal</h1>
        <div id="acc_info" style="float:right; margin-right: 5px;">
        <p style="margin-top: 0px; margin-bottom: 0px; text-align:right;">Logged in as: <?php echo $acc_data["first_name"]; ?> <?php echo $acc_data["last_name"]; ?></p>
        <a style="float:right;"href="main.php?logout=true">Log Out</a>
        </div>
    </header>
    <body>
        <br>
        <h2 id="experience-name" style="margin:auto; width:50%; text-align:center;"><?php echo $activity_data['name'];?></h2>
        <p style="margin:auto;margin-bottom:20px; text-align:center;"><?php echo $activity_data['description'];?></p>
        <p style="margin:auto; text-align:center;">&ensp;Organized by: <?php echo $activity_data['first_name'];?> <?php echo $activity_data['last_name'];?>&ensp; Email: <?php echo $activity_data['email'];?></p>
        <p style="margin:auto; text-align:center;"> Service Hours Awarded: <?php echo $activity_data['hours'];?> Hours</p>
        <p style="margin:auto; margin-bottom: 20px; text-align:center;">Activity Duration: <?php echo date("m/d/Y", strtotime($activity_data['start_date']));?> - <?php echo date("m/d/Y", strtotime($activity_data['end_date']));?></p>
        <hr>
        <?php if($is_organizer):?>
            <h4 style="text-align:center;">Activity Participants:</h4>
            <div id="activity-box" style="height: 500px; overflow:auto;">
                <table>
                    <thead style="text-align:center;">
                        <tr><td>Name</td><td>Information</td><td>Email</td></tr>
                    </thead>
                    <?php
                        $sql = "SELECT * FROM activity_participants
                                INNER JOIN accounts
                                ON accounts.account_id = activity_participants.account_id
                                WHERE activity_participants.activity_id = $activity_id";
                        $result = mysqli_query($mysqli, $sql);
                        while($participant = mysqli_fetch_assoc($result))
                        {
                            echo "<tr><td>$participant[first_name] $participant[last_name]</td><td>$participant[extra_info]</td><td style=\"text-align:right;\">Email: $participant[email]</td></tr>";
                        }
                    ?>
                </table>
            </div>
        <?php else: ?>
            <div id="activity-box">
                <h3>Sign-Up</h3>
                <form method="post">
                    <label for="extra_info">Share any information with the activity organizer:</label>
                    <br>
                    <textarea rows="5" cols="40" name="extra_info" style="margin-bottom: 15px;"></textarea>
                    <input type="submit" value="Sign Up" style="margin:5px;" <?php if($disabled){echo "disabled";}?>>
                </form>
            </div>
        <?php endif;?>
    </body>
</html>