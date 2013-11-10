Lighttpd external-auth
======================


Summary
-------

[external-auth.lua](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=external-auth.lua)
is a lighttpd magnet script:

- Originally written for openid
- Providing access control via openid, oauth and the likes
- Allowing per-user filtering
- Protecting static content / web apps unaware of security otherwise

Demo
----

Here is [a protected directory](/demo3): You will be asked to log in using
the method of your choice to see the content. This should give you a clear
idea about the possibilities of the script.

How do I use this?
------------------

Let us assume that you created a dedicated subdomain `login.example.com`
for hosting the login page (for other situations, read the tutorials).

1. Copy
   [external-auth.lua](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=external-auth.lua).
   to `/etc/lighttpd/lua/external-auth.lua`.

2. Install the lua dependencies. On debian:

        sudo aptitude install luarocks
        sudo luarocks install luacrypto

3. Copy the files of
   [example-loginpage](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=tree;f=example-loginpage)
   to the root of your subdomain `login.example.com`

4. Follow the installation instructions for hybridauth indicated in the
   file
   [hybridauth/README.md](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=example-loginpage/hybridauth/README.md)

5. Change the variable `$magnet_config["domain"]` to reflect your domain
   in the file
   [magnet.php](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=example-loginpage/magnet.php)

        $magnet_config["domain"] = "example.com";

6. Activate `mod_setenv` and `mod_magnet` in `/etc/lighttpd/lighttpd.conf`

        server.modules = (
            ...
            "mod_setenv",
            "mod_magnet",
            ...
        )

7. Create your authentication file `/etc/lighttpd/lua/external-auth/auth.lua`

        config["login_url"] = "https://login.example.com"
        config["authorized_identities"] = { }
        config["authorized_identities"]["your.mail@gmail.com (Google)"] = true

8. Protect your content using lighttpd conditionals

        $HTTP["url"] =~ "/protected.*" {
            setenv.add-environment = ( "EXTERNAL_AUTH_CONFIG" =>
            "/etc/lighttpd/lua/external-auth/auth.lua" )
            magnet.attract-physical-path-to = (
            "/etc/lighttpd/lua/external-auth.lua" )
        }

Source/License
--------------

Source available at
[git.chmd.fr](https://git.chmd.fr/?p=lighttpd-external-auth.git), mirrored on
[github](https://github.com/chmduquesne/lighttpd-external-auth).

    git clone https://git.chmd.fr/lighttpd-external-auth.git

License MIT

How does this work?
-------------------

Here is the intended work flow:
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
page can read it.  Obviously, this means that the login page has to be
hosted to be on the same lighttpd server, so that it can read the shared
secret.

Tutorial demo1: The basics
--------------------------

First, you should [logout](https://login.chmd.fr/?logout=true), Then you
can try to visit [demo 1](/demo1): This page is protected by a lame login
page. POST-ing "login=Guest" signs you on as "Guest", and POST-ing
"logout=logout" signs you off.

1. Install
   [external-auth.lua](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=external-auth.lua).
   We will copy it to `/etc/lighttpd/lua/external-auth.lua`, but any path
   readable by lighttpd would work.

2. Install the lua dependencies. On debian:

        sudo aptitude install luarocks
        sudo luarocks install luacrypto

3. Activate `mod_setenv` and `mod_magnet` in `/etc/lighttpd/lighttpd.conf`

        server.modules = (
            ...
            "mod_setenv",
            "mod_magnet",
            ...
        )

4. Put the content you want to protect in a conditional. Here, we are
   going to protect every url starting with
   [http://lighttpd-external-auth.chmd.fr/demo1](https://lighttpd-external-auth.chmd.fr/demo1).
   To do so, we load the script with `magnet.attract-physical-path-to` in
   the conditional:

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
            # Set the document root
            server.document-root = "/home/www/sites/lighttpd-external-auth.chmd.fr/"
            # Conditional where we load the script
            $HTTP["url"] =~ "/demo1.*" {
                    magnet.attract-physical-path-to = (
                    "/etc/lighttpd/lua/external-auth.lua" )
                }
            }
            ...
        }

5. We need to set up an authentication page. By default, the script
   redirects unauthenticated users to the page `/login.php`. For now, we
   will follow these defaults. We will first copy the file
   [magnet.php](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=blob_plain;f=magnet.php)
   to the document-root. Then, we edit `login.php`, the page that actually
   performs the login.

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

That is it!

Tutorial demo2: Access Control
------------------------------

The next step will be about limiting the access to some users. We now want
to protect the urls starting with
[http://lighttpd-external-auth.chmd.fr/demo2](/demo2), and give
access to "VIP", but not to "Guest". This is done by having the script
load extra lua code. To tell the script what lua code to load, we use
environment variables.

1. Make a second lighttpd conditional for filtering demo2, and load the
   script with `magnet-attract-physical-path-to`:

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
        ...
            $HTTP["url"] =~ "/demo2.*" {
                $HTTP["url"] != "/demo2/login.php" {
                    magnet.attract-physical-path-to = (
                    "/etc/lighttpd/lua/external-auth.lua" )
                }
            }

2. BEFORE loading the script, set the environment variable
   `EXTERNAL_AUTH_CONFIG` to tell the script where to load our extra lua
   code from. We will put the code in the file
   `/etc/lighttpd/lua/external-auth/demo2.lighttpd-external-auth.chmd.fr.lua`

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
        ...
            $HTTP["url"] =~ "/demo2.*" {
                $HTTP["url"] != "/demo2/login.php" {
                    setenv.add-environment = ( "EXTERNAL_AUTH_CONFIG" =>
                    "/etc/lighttpd/lua/external-auth/demo2.lighttpd-external-auth.chmd.fr.lua" )
                    magnet.attract-physical-path-to = (
                    "/etc/lighttpd/lua/external-auth.lua" )
                }
            }

3. Edit
   `/etc/lighttpd/lua/external-auth/demo2.lighttpd-external-auth.chmd.fr.lua`.
   There are mainly two settings that are interesting to modify: the
   location of the login page, and the user authorized to see the content.
   We are interested in the latter. Basically, if we don't specify
   anything, everyone is authorize. However, if we want to filter users,
   we initialize `config["authorized_identities"]` to an empty table, and
   then we set `config["authorized_identities"]["VIP"]` to `true`. This
   has the effect of authorizing the user "VIP" (and only this user) to
   see the content protected by the script.

        config["authorized_identities"] = { }
        config["authorized_identities"]["VIP"] = true

4. Since we can choose another location for the login page, let us put it
   in `/demo2/login.php`. Edit
   `/etc/lighttpd/lua/external-auth/demo2.lighttpd-external-auth.chmd.fr.lua`
   and assign the variable `config["login_url"]`:

        config["login_url"] = "/demo2/login.php"
        config["authorized_identities"] = { }
        config["authorized_identities"]["VIP"] = true

5. We have to be careful not to place the login page in an unreachable
   place. Edit `/etc/lighttpd/lighttpd.conf`, and modify the conditional
   such that this page is reachable:

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
            ...
            $HTTP["url"] =~ "/demo2.*" {
                # make an exception for "/demo2/login.php"
                $HTTP["url"] != "/demo2/login.php" {
                    setenv.add-environment = ( "EXTERNAL_AUTH_CONFIG" =>
                    "/etc/lighttpd/lua/external-auth/demo2.lighttpd-external-auth.chmd.fr.lua" )
                    magnet.attract-physical-path-to = (
                    "/etc/lighttpd/lua/external-auth.lua" )
                }
            }

6. Last, create the login page `/demo2/login.php` (don't forget to copy
   `magnet.php` in `demo2/`)

        <?php
        // We need the functions in the script 'magnet.php', that we installed
        // along with our login page
        require_once('magnet.php');
        
        if (isset($_POST["login"])){
            // If we receive POST "login=Guest", we login as Guest
            if ($_POST["login"] == "Guest") {
                magnet_authentify("Guest");
            }
            // If we receive POST "login=VIP", we login as VIP
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
        // If we receive POST logout, the logout is performed
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

Done!

Tutorial demo3: real openid/oauth
---------------------------------

Ok, so we have written lame login pages. What about openid and oauth? As you
might expect, login pages can get a little bit fancy and complicated, so we
will not get into many details. This project provides such a login page (a copy
of the one that lives on [login.chmd.fr](https://login.chmd.fr)). It protects
[demo3](/demo3) How to set it up is explained in the README files of
[example-loginpage](https://git.chmd.fr/?p=lighttpd-external-auth.git;a=tree;f=example-loginpage).
We will just quickly give the configuration:

1. `/etc/lighttpd/lighttpd.conf`

        $HTTP["host"] == "lighttpd-external-auth.chmd.fr" {
            ...
            $HTTP["url"] =~ "/demo3.*" {
                setenv.add-environment = ( "EXTERNAL_AUTH_CONFIG" =>
                "/etc/lighttpd/lua/external-auth/demo3.lighttpd-external-auth.chmd.fr.lua" )
                magnet.attract-physical-path-to = (
                "/etc/lighttpd/lua/external-auth.lua" )
            }
        }

2. `/etc/lighttpd/lua/external-auth/demo3.lighttpd-external-auth.chmd.fr.lua`

        config["login_url"] = "https://login.chmd.fr"

Conclusion
----------

Have fun with this!

<div class="comments">
<h2>Comments</h2>
    <script id='fbnrl3e'>(function(i){var f,s=document.getElementById(i);f=document.createElement('iframe');f.src='//api.flattr.com/button/view/?uid=chmd&button=compact&url='+encodeURIComponent(document.URL);f.title='Flattr';f.height=20;f.width=110;f.style.borderWidth=0;s.parentNode.insertBefore(f,s);})('fbnrl3e');</script>
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_identifier = "lighttpd-external-auth.chmd.fr";
        (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//chmd.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
      })();
    </script>
</div>
