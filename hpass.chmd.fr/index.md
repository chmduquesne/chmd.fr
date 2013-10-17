Hpass
=====

TL,DR: where is the mobile version?
-----------------------------------

[Here](http://git.chmd.fr/?p=hpass.git;a=blob_plain;f=js/index.html;hb=HEAD)

Summary
-------

Hpass allows you to use different passwords on every websites, without
having to remember them. Instead, you memorize one **unique** master
password. Using your master password and the domain name of the web site,
Hpass generates a unique password for the web site.

Inspiration
-----------

Hpass was inspired by various extensions and bookmarklets (the most famous
being [Supergenpass](http://supergenpass.com)). However Supergenpass sucks
in many aspects:

  1. The algorithm is not proven to be secure (See
     [why](http://stackoverflow.com/a/3484954/628786)).
  2. Supergenpass bookmarklet implementation can leak your
     master password (See
     [how](http://akibjorklund.com/2009/supergenpass-is-not-that-secure)).
  3. The algorithm is not easily reproducable (no shell one-liners are
     available)

Specification
-------------

The way Hpass generates password can be reproduced in one single command
line:

    echo -n $DOMAIN | \
        openssl dgst -$HASHTYPE -hmac $MASTER -binary | \
        openssl enc -base64 | \
        cut -c1-$LEN

Where:

  * \$DOMAIN is the domain name of the website;
  * \$HASHTYPE is a type of checksum (md5, sha1, sha256, sha512...);
  * \$MASTER is your master password;
  * and \$LEN is the length of the desired password.

Implementations
---------------

Because we need our passwords in different situations, Hpass is
implemented for several platforms:

  - There's an [android
    version](https://play.google.com/store/apps/details?id=fr.chmd.hpass).
    It will open links shared from the browsers, but it can also open a
    link scanned through a barcode (provided you did not already set a
    default browser).
  - On a day to day basis, the author uses a shell implementation that
    sits in the tray. It stores/gets the password from gnome-keyring.
  - A [javascript implementation]
    (http://git.chmd.fr/?p=hpass.git;a=blob_plain;f=js/index.html;hb=HEAD)
    also exists, for when both your computer and your cellphone are out of
    reach.

What needs to be improved
-------------------------

There is no "salt" in the implementation. Basically, for every domain
name, a random string could be appended to the master password. This would
improve the security, but expose the uses to data loss and require
synchronization between the devices used to generate the passwords. The
advantages you could get out of it:

  1. Modifying only one password would be easier: right now, it is not
     possible to modify only **one** of your passwords. When you lose
     one of your passwords, you need to change your master password to
     change it, which implies changing every password that you manage
     with Hpass.
  2. Stealing your master password would not enable an attacker to steal
     all of your passwords, since this attacker would also need these
     "salts" to generate your passwords.

However, synchronisation is complicated. It is a matter of trust in third
parties to store your salts, and of confidence that you will not
experiment data loss. This latter aspect is extremely important: should
you lose the salts, you would lose all your passwords!

Repository
----------
You can browse the code on
[git.chmd.fr](http://git.chmd.fr/?p=hpass.git;a=summary). To clone
it:

    git clone http://git.chmd.fr/hpass.git


License/Credits
-------

Useful informations:

  * Hpass is delivered under the GPLv3+.
  * The icon I have been using for the Hpass android application is from
    the 'lock' from the [faenza gtk icon
    theme](https://code.google.com/p/faenza-icon-theme).
