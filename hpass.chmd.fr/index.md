Hpass
=====

Intro
-----

Everyone should be using a password manager, but most people don't,
because of the fears of:

- not having their passwords when they need them
- losing their encrypted passwords

Hpass is a password manager that does not store passwords. Instead, it
generates them. You provide it with a **master password** and a **domain
name**, and it returns a secure password for you to use with that domain.

Implementations
---------------

  - Check first the [javascript
    implementation](http://git.chmd.fr/?p=hpass.git;a=blob_plain;f=js/index.html;hb=HEAD).
    It is ideal when your own computer and smartphone are out of reach.
  - There's also an [android
    version](https://play.google.com/store/apps/details?id=fr.chmd.hpass).
    It will open links shared from the browsers, but it can also open a
    link scanned through a barcode (provided you did not already set a
    default browser).
  - On a day to day basis, the author uses a shell implementation that
    sits in the tray. It stores/gets the password from gnome-keyring.

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

Upcoming features
-----------------

Currently, if one of your generated password is compromised, there is no
way to change it in Hpass without changing your master password. To solve
this problem, next versions of Hpass should include a concept of password
version. The password version will be number, starting at 0 and being
incremented everytime you want to change this password only. Hpass would
remember this number and use it in the password generation.

The reason why the version of a password will be deterministic is to
prevent you against data loss: if you lose the data associated with the
app, you will just have to bump a few versions until you can regenerate
the correct password!

In order to make it convenient, Hpass will synchronize the version numbers
through a secure anonymous data store. This data store should be seen as a
synchronization method, not as a backup! More info on
[minibackup.chmd.fr](http://minibackup.chmd.fr).

Repository
----------
You can browse the code on
[git.chmd.fr](http://git.chmd.fr/?p=hpass.git;a=summary). To clone
it:

    git clone http://git.chmd.fr/hpass.git

What about supergenpass?
------------------------

Hpass was inspired by various extensions and bookmarklets (the most famous
being [Supergenpass](http://supergenpass.com), but there are others).
Supergenpass sucks in many aspects:

  1. The algorithm is not proven to be secure (See
     [why](http://stackoverflow.com/a/3484954/628786)).
  2. Supergenpass bookmarklet implementation can leak your
     master password (See
     [how](http://akibjorklund.com/2009/supergenpass-is-not-that-secure)).
  3. The algorithm is not easily reproducable (no shell one-liners are
     available)

License/Credits
---------------

Useful informations:

  * Hpass is delivered under the GPLv3+.
  * The icon used for the Hpass android application is the 'lock' from the
    [faenza gtk icon theme](https://code.google.com/p/faenza-icon-theme).

<div class="comments">
<h2>Comments !</h2>
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_identifier = "hpass.chmd.fr";
        (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//chmd.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
      })();
    </script>
</div>
