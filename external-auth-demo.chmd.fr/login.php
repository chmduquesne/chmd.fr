<?php
require_once('magnet.php');
if (isset($_POST["login"])){
    magnet_authentify('Guest');
    if (isset($_GET["orig_url"])) {
        $orig_url = $_GET["orig_url"];
        if (matches_domain($orig_url)) {
            header("Location: $orig_url");
        }
    }
}
else {
    if (isset($_POST["logout"])){
        magnet_deauthentify();
    }
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>login</title>
</head>
<body>
</form>
    <form action="" method="post">
        <button name="login" value="login">login</button>
    <form>
    <form action="" method="post">
        <button name="logout" value="logout">logout</button>
    <form>
</body>
