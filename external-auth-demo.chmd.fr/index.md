Lighttpd external-auth
======================

Summary
-------

`external-auth.lua` is a magnet script originally written in order to
allow access control to certain pages of a lighttpd server through an
openid-based authentication mechanism. But it can do much more: actually,
any external means of authentication (such as oauth or even ldap) can be
used with this script.

Howto
-----

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

4. We now need to set up an authentication page. By default, the script
   redirects unauthenticated users to the page `/login.php`. For now, we
   will follow these defaults. We will first copy the file
   [magnet.php](http://fix.me) to the document-root.

5. We now edit








Goal of this magnet script
==========================

The goal of this magnet script is to let you restrict access of certain
pages of your lighttpd server through an external mechanism. This external
mechanism can be openid, but also oauth, or any other method.

How it works
============

When a request is made, the script checks for a cookie.



* [demo 1](/demo1)

* [demo 2](/demo2)
* [demo 3](/demo3)
