Hpass
=====

Summary
-------

Hpass is a different approach to passwords management. It allows you to
use different passwords on every websites, without having to remember
them. Instead, you memorize one **unique** master password. Using your
master password and the domain name of the web site, Hpass generates a
unique password for the web site.

Inspiration
-----------

Hpass was inspired by various extensions and bookmarklets (the most famous
being [Supergenpass](http://supergenpass.com)). The reason why I wrote
Hpass is because Supergenpass sucks in many aspects:

  1. The algorithm is not proven to be secure (See
     [why](http://stackoverflow.com/a/3484954/628786)).
  2. Supergenpass bookmarklet implementation can leak your
     master password (See
     [how](http://akibjorklund.com/2009/supergenpass-is-not-that-secure)).
  3. I wanted to be able to regenerate any of my passwords using a
     simple command line.

Specification
-------------

The way Hpass generates password is dead simple, and can be reproduced
easily:

    echo -n $DOMAIN | \
        openssl dgst -$HASHTYPE -hmac $MASTER -binary | \
        openssl enc -base64 | \
        cut -c1-$LEN

Where \$DOMAIN is the domain name of the website, \$HASHTYPE is a type of
checksum (md5, sha1, sha256, sha512...), \$MASTER is your
master password, and \$LEN is the length of the desired password.

Implementations
---------------

Because we need our passwords in different situations, I implemented hpass
for several platforms:

  - There's an android version on the google play store. It will open
    links shared from the browsers, but it can also open a link scanned
    through a barcode (provided you did not already set a default
    browser).
  - On a day to day basis, I use a shell implementation that sits in the
    tray. It stores/gets the password from gnome-keyring.
  - I also wrote a javascript implementation, for when both your
    computer and your cellphone are out of reach.

What needs to be improved
-------------------------

I did not add a "salt" in the implementation. Basically, for every domain
name, we could append to the master password a random string that would
stored and synchronized in a "cloudy" way. This would allow two things:

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
you lose the salts, you would lose all your passwords!  I might add
support for that some day, but right now, I prefer to keep things simple
:)

Repository
----------
You can browse the code on
[gitweb.chmd.fr](http://gitweb.chmd.fr/?p=hpass.git;a=summary). To clone
it:

    git clone http://git.chmd.fr/hpass.git


License/Credits
-------

Useful informations:

  * Hpass is delivered under the GPLv3+.
  * The icon I have been using for the Hpass android application is from
    the 'lock' from the [faenza gtk icon
    theme](https://code.google.com/p/faenza-icon-theme).
