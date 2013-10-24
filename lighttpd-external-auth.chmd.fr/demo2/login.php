<?php
// We need the functions in the script 'magnet.php', that we installed
// along with our login page
require_once('magnet.php');

// If we receive a POST "login=Guest", the login is performed
if (isset($_POST["login"])){
    if ($_POST["login"] == "Guest") {
        magnet_authentify("Guest");
    }
    if ($_POST["login"] == "VIP") {
        magnet_authentify("VIP");
    }
    if (isset($_GET["orig_url"])) {
        $orig_url = $_GET["orig_url"];
        if (matches_domain($orig_url)) {
            header("Location: $orig_url");
        }
    }
}
// If the users POSTs logout, the logout is performed
else {
    if (isset($_POST["logout"])){
        magnet_deauthentify();
    }
}
// The rest is just html with two buttons, login and logout, that POST
// the appropriate values.
?>

<html>
<head>
    <meta charset="utf-8">
    <title>login</title>
</head>
<body>
</form>
    <form action="" method="post">
        <button name="login" value="Guest">login as "Guest"</button>
    <form>
    <form action="" method="post">
        <button name="login" value="VIP">login as "VIP"</button>
    <form>
    <form action="" method="post">
        <button name="logout" value="logout">logout</button>
    <form>
</body>
