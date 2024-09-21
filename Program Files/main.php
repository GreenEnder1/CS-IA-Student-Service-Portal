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
    if(isset($_GET["logout"]))
    {
        session_destroy();
        header("Location: login.php");
    }
    $created = false;
    if(isset($_GET['created']))
    {
        $created = $_GET['created'];
    }
    $signup = false;
    if(isset($_GET['signup']))
    {
        $signup = $_GET['signup'];
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
        <?php if($created): ?>
            <em style="color:#11FF11; margin:auto; margin-left: 25%; width:50%; text-align:center; display:inline-block;">Experience Created!</em>
        <?php endif; ?>
        <?php if($signup): ?>
            <em style="color:#11FF11; margin:auto; margin-left: 25%; width:50%; text-align:center; display:inline-block;">Sign Up Successful!</em>
        <?php endif; ?>
        <br>
        <div id="experiences" style="margin-left:7.5%;">
            <h4 style="text-align: center; margin-bottom:0px;">Available Service Experiences</h4>
            <p style="text-align: center; margin-bottom:0px; margin-top:0px;">You are not signed up for these experiences yet. You can learn more about each experience and sign up for them by clicking View.</p>
            <table style="width:100%;">
                <?php
                    $sql = "SELECT service_activities.name, service_activities.activity_id FROM service_activities
                            LEFT JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            AND activity_participants.account_id = {$acc_data["account_id"]}
                            WHERE account_id IS NULL AND organizer_id != {$acc_data["account_id"]} AND start_date >= CURRENT_TIMESTAMP
                            ORDER BY start_date DESC";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Future Experiences</h5>These Experiences have not started yet and you can sign up for them.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td></tr>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}

                    $sql = "SELECT service_activities.name, service_activities.activity_id FROM service_activities
                            LEFT JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            AND activity_participants.account_id = {$acc_data["account_id"]}
                            WHERE account_id IS NULL AND organizer_id != {$acc_data["account_id"]} AND start_date < CURRENT_TIMESTAMP AND end_date >= CURRENT_TIMESTAMP
                            ORDER BY start_date DESC";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Ongoing Experiences</h5> These experiences have already started, but you can still sign up for them.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td></tr>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}
                    
                    $sql = "SELECT service_activities.name, service_activities.activity_id FROM service_activities
                            LEFT JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            AND activity_participants.account_id = {$acc_data["account_id"]}
                            WHERE account_id IS NULL AND organizer_id != {$acc_data["account_id"]} AND end_date < CURRENT_TIMESTAMP
                            ORDER BY start_date DESC";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Complete Experiences</h5> These experiences have already ended and you can no longer sign up for them.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td></tr>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}
                ?>
            </table>
        </div>
        <div id="experiences">
            <h4 style="text-align:center; margin-bottom:0px;">Active Service Experiences</h4>
            <p style="text-align: center; margin-bottom:0px; margin-top:0px;">You have signed up for these experiences. To see more information about them, click the View button.
            <table style ="width:100%;">
            <?php
                    $sql = "SELECT service_activities.activity_id, service_activities.name FROM service_activities
                            INNER JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            WHERE account_id = {$acc_data["account_id"]} AND organizer_id != {$acc_data["account_id"]} AND start_date >= CURRENT_TIMESTAMP";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Future Experiences</h5>These Experiences have not started yet.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}

                    $sql = "SELECT service_activities.activity_id, service_activities.name FROM service_activities
                            INNER JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            WHERE account_id = {$acc_data["account_id"]} AND organizer_id != {$acc_data["account_id"]} AND start_date < CURRENT_TIMESTAMP AND end_date >= CURRENT_TIMESTAMP";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Ongoing Experiences</h5>These Experiences have begun.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}

                    $sql = "SELECT service_activities.activity_id, service_activities.name FROM service_activities
                            INNER JOIN activity_participants
                            ON service_activities.activity_id = activity_participants.activity_id
                            WHERE account_id = {$acc_data["account_id"]} AND organizer_id != {$acc_data["account_id"]} AND end_date < CURRENT_TIMESTAMP";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    echo "<tr><td style=\"text-align:center;\"><h5 style=\"margin-bottom:0px;\">Completed Experiences</h5>These Experiences have ended.</td></tr>";
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td>";
                        $is_empty = false;
                    }
                    if($is_empty) {echo "<tr><td>Nothing here :(</td></tr>";}
                ?>
            </table>
        </div>
        <div id="experiences">
            <h4 style="text-align:center; margin-bottom: 0px;">My Service Experiences</h4>
            <p style="text-align: center; margin-bottom:0px; margin-top:0px;">These are the experiences you have created. To create a new experience, click the Create Experience button. To view the participants in your experiences, click the View button.
            <table style ="width:100%;">
            <?php
                    $sql = "SELECT service_activities.activity_id, service_activities.name FROM service_activities 
                            WHERE organizer_id = {$acc_data["account_id"]}";
                    $result = mysqli_query($mysqli, $sql);
                    $is_empty = true;
                    while($activity = mysqli_fetch_assoc($result))
                    {
                        echo "<tr><td>$activity[name]<a href=\"service.php?id=$activity[activity_id]\"><button style=\"float: right;\" onclick=\"\">View</button></a></td>";
                        $is_empty = false;
                    }
                ?>
            </table>
            <td><tr><a href="create_activity.php"><button style="width:100%;" onclick="">Create Experience</button></a></tr></td>
        </div>
    </body>
</html>