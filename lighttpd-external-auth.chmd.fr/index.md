Lighttpd external-auth
======================


Summary
-------

[external-auth.lua](https://git.chmd.fr/?p=lighttpd-external-auth;a=blob_plain;f=external-auth.lua;hb=HEAD)
is a lighttpd lua magnet script:

- Originally written for openid
- Providing access control via openid, oauth and the likes
- Protecting static content / web apps unaware of security otherwise

Demos
-----
* [demo 1](/demo1): This page is protected by a lame login page. POST-ing
  "login=Guest" signs you on as "Guest", and POST-ing "logout=logout"
  signs you off.
* [demo 2](/demo2): This page demonstrates per-user access control. You
  cannot login as "Guest", but you can do it as "VIP".
* [demo 3](/demo3): Before trying this one, you should
  [logout](https://login.chmd.fr/?logout=true), because
  no access control will performed. It presents you with a nice login page
  that actually performs some openid/oauth checks.

Source/License
--------------

Source available at
[git.chmd.fr](https://git.chmd.fr/?p=lighttpd-external-auth), mirrored on
[github](https://github.com/chmduquesne/lighttpd-external-auth).

    git clone https://git.chmd.fr/lighttpd-external-auth

License MIT

How does this work?
-------------------
Here is the basic work flow:

1. The user tries to access protected content. The magnet script
   intercepts the request and checks for a username and an authentication
   token in the user's cookies. As no authentication has occurred yet, this
   fails and the user is redirected to a login page.
2. On the login page, the user goes through an authentication process.
   This part has actually nothing to do with this script. The only thing
   that matters is that, at the end of this authentication process, when
   the authentication is successful, the login page sets the appropriate
   username and authentication token in the cookies of the user's browser,
   and then redirects the user to the original url.
3. This time, when the script checks, it finds the username and the
   authentication token in the user's cookies. It checks that the token is
   valid, then checks that this username is allowed to access the content.
   If both conditions are satisfied, the script stops there and lets
   lighttpd deliver the content normally.

The authentication token that is mentioned in this work flow is a
hmac-sha1 signature, where the message to be signed is the username
appended with a timestamp (the user will be allowed to access the content
only within the time window where this timestamp is considered valid), and
the secret is a random string that is shared between the magnet and the
login page. This random string is initialized when the magnet script is
loaded for the first time and is stored in a file, such that the login
page can read it.  Obviously, this means that the login page has somehow
to be on the same lighttpd server.

Howto demo1: The basics
-----------------------

1. You first need to install the script
   [external-auth.lua](https://git.chmd.fr/?p=lighttpd-external-auth;a=blob_plain;f=external-auth.lua;hb=HEAD).
   We will copy it to `/etc/lighttpd/lua/external-auth.lua`, but any path
   readable by lighttpd would work.

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
   [http://lighttpd-external-auth.chmd.fr/demo1](https://lighttpd-external-auth.chmd.fr/demo1).
   To do so, we load the script with `magnet.attract-physical-path-to` in
   the conditional.

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
            server.document-root = "/home/www/sites/lighttpd-external-auth.chmd.fr/"
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
   [magnet.php](https://git.chmd.fr/?p=lighttpd-external-auth;a=blob_plain;f=magnet.php;hb=839424ae7fa7a83018d81f56c9a142bb4fb6b006)
   to the document-root.

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

6. That is it! You can now try to visit [demo1](/demo1). You should be
   redirected to your first login page.

Howto demo2: Access Control
---------------------------

The next step will be about limiting the access to some users. We now want
to protect the urls starting with
[http://lighttpd-external-auth.chmd.fr/demo2](/demo2).


