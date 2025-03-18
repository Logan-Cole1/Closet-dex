<html>
<head>
    <link rel="stylesheet" href="newstyle.css">
</head>

<body>
    Choose Font Family:
    <button onclick="chooseFont('sans-serif')">Sans Serif</button>
    <button onclick="chooseFont('serif')">Serif</button>
    <button onclick="chooseFont('monospace')">Monospace</button>
    <button onclick="chooseFont('cursive')">Cursive</button>
    <button onclick="chooseFont('fantasy')">Fantasy</button>

    <div id="center-box">
        <img id="login-page-logo" src="logo.png">
        <div id="login-box">
            <form action="login.php" method="post">
                <label for="username">Username: </label>
                <input type="text" name="username" placeholder="Your Name"><br>
                <label for="password">Password: </label>
                <input type="password" name="password" placeholder="Your Password"><br>
                <input type="submit" value="Login" name="login">
                <input type="submit" value="Register Account" name="register">
            </form>
        </div>
    </div>

    <script>
        function chooseFont(id) {
            var elements = document.querySelectorAll("#center-box *");
            for (var i = 0; i < elements.length; i++) {
                elements[i].style.fontFamily = id;
                console.log(elements[i]);
            }
        }
    </script>
</body>
</html>

<?php
// filepath: m:\my_web_files\classdb\Closet-dex\login.php
session_start();
require "db.php";

if (isset($_POST["login"])) {
    if (authenticate_customer($_POST["username"], $_POST["password"]) == 1) {
        $_SESSION["username"] = $_POST["username"];
        header("LOCATION:main.php");
        return;
    } else {
        echo '<p style="color:red">Incorrect username or password</p>';
    }
}

if (isset($_POST["register"])) {
    header("LOCATION:registration.php");
}

if (isset($_POST["logout"])) {
    session_destroy();
}
?>