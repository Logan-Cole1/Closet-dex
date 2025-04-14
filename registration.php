<!-- registration.php
 * This file is the registration page of the application.
 * It allows users to create a new account by entering a username and password.
 * If the user already exists, an error message is displayed.
 -->


<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <title>Closet-Dex | Register Account</title>
</head>

<body>
    <h1>
        <div id="center-title">
            Registration Page
        </div>
    </h1>

    <div id="center-box">
        <div id="login-box">
            <form action="registration.php" method="post">
                <?php
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo '<p style="color:red">' . $error . '</p>';
                    }
                }
                if (!empty($errorPass)) {
                    foreach ($errorPass as $error) {
                        echo '<p style="color:red">' . $error . '</p>';
                    }
                }
                ?>
                <input class="textbox" placeholder="Username" type="text" name="username" required>
                <br>
                <input class="textbox" placeholder="Password" type="password" name="password" required>
                <br>
                <input class="textbox" placeholder="Confirm Password" type="password" name="password_confirmation" required>
                <br>
                <input class="button" type="submit" value="Register" name="register">
            </form>
            <a href="index.php"><button class="button">Cancel</button></a>
        </div>
    </div>
</body>

</html>


<?php 

require_once __DIR__ . "/db.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // Validate password
    $password_errors = validate_password($password);

    // Check if password and confirmation match
    if ($password !== $password_confirmation) {
        $password_errors[] = "Passwords do not match.";
    }
}

$errors = [];
$errorPass = [];

if (isset($_POST["register"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['password_confirmation'];

    // Step 1: Check if username already exists
    $check = user_exists($username);

    // If the username already exists, display the error and stop further checks
    if ($check) {
        echo '<p style="color:red">Username "' . $username . '" already exists. Try again.</p>';
		exit;
    }

    // Validate password if necessary (this happens only after checking the username)
    if (empty($errors)) {
        $errorPass = validate_password($password);
    }

	
    // If password validation has errors, display them
	if (!empty($errorPass) || !empty($errors)) {
		foreach ($errorPass as $error) {
			echo '<p style="color:red">' . $error . '</p>';
		}
	}

	    // Step 2: Validate password only if username is available
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
	
	if (!empty($errors)) {
		echo '<p style="color:red">' . $errors[0] . '</p>';
	}

    // Step 3: Proceed with creating the user if no errors
    if (empty($errors) && empty($errorPass)) {
        $check = create_user($username, $password);
        if ($check) {
            header("Location: index.php");
            exit;
        }
    }
}


?>

