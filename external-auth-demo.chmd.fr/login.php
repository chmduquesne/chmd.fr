<?php
require_once('magnet.php');
if (isset($_POST["authentify"])){
    magnet_authentify('Guest');
    if (isset($_GET["orig_url"])) {
        $orig_url = $_GET["orig_url"];
        if (matches_domain($orig_url)) {
            header("Location: $orig_url");
        }
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
        <button name="authentify" value="authentify">authentify me!</button>
    <form>
</body>
