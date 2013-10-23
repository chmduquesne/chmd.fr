Lighttpd external-auth
======================

Summary
-------

`external-auth.lua` is a magnet script originally written in order to
allow access control to certain pages of a lighttpd server through an
openid-based authentication mechanism. But it can do much more: actually,
any external means of authentication (such as oauth or even ldap) can be
used with this script.

Demos
-----
* [demo 1](/demo1): This page is protected by a lame login page with no
  security whatsoever.
* [demo 2](/demo2): This page is protected via a login page, that requires
  you to oauth or openid to see the content.

Howto demo1: The basics
-----------------------

1. You first need to install the script
   [external-auth.lua](http://fix.me). We will copy it to
   `/etc/lighttpd/lua/external-auth.lua`, but any path readable by
   lighttpd would work.

2. Then, activate `mod_setenv` and `mod_magnet` in
   `/etc/lighttpd/lighttpd.conf`

        server.modules = (
            ...
            "mod_setenv",
            "mod_magnet",
            ...
        )

3. Put the content you want to protect in a conditional. Here, we are
   going to protect every url starting with
   [http://external-auth-demo.chmd.fr/demo1](https://external-auth-demo.chmd.fr/demo1).
   To do so, we load the script with `magnet.attract-physical-path-to` in
   the conditional.

        $HTTP["host"] == "external-auth-demo.chmd.fr" {
            server.document-root = "/home/www/sites/external-auth-demo.chmd.fr/"
            $HTTP["url"] =~ "/demo1.*" {
                    magnet.attract-physical-path-to = (
                    "/etc/lighttpd/lua/external-auth.lua" )
                }
            }
            ...
        }

4. We need to set up an authentication page. By default, the script
   redirects unauthenticated users to the page `/login.php`. For now, we
   will follow these defaults. We will first copy the file
   [magnet.php](http://fix.me) to the document-root.

5. We now edit `login.php`, the page that actually performs the login.

        <?php
        // We need the functions in the script 'magnet.php', that we installed
        // along with our login page
        require_once('magnet.php');
        
        // If we receive a POST "login=Guest", the login is performed
        if (isset($_POST["login"])){
            if ($_POST["login"] == "Guest") {
                magnet_authentify("Guest");
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
                <button name="logout" value="logout">logout</button>
            <form>
        </body>

6. That is it! You can now try to visit
   [demo1](https://external-auth-demo.chmd.fr/demo1). You should be
   redirected to your first login page.

Howto demo2: Access Control
---------------------------

The next step will be about limiting the access to some users. We now want
to protect the urls starting with
[http://external-auth-demo.chmd.fr/demo2](https://external-auth-demo.chmd.fr/demo1).







Goal of this magnet script
==========================

The goal of this magnet script is to let you restrict access of certain
pages of your lighttpd server through an external mechanism. This external
mechanism can be openid, but also oauth, or any other method.

How it works
============

When a request is made, the script checks for a cookie.



* [demo 3](/demo3)
