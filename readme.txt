=== SpoofProof===
Contributors: Jerry Hayward
Tags: SpoofProof, Security, injection, anti-injection, anti-javascript-Injection, anti-PHP-injection
Requires at least: 4.3
Tested up to: 4.6
Stable tag: 4.6
License: GPLv2 or later

SpoofProof alters the WP login screen using a web service to verify that you are not being attacked by  spoofing, phishing, or Man in the middle.

== Description ==

SpoofProof alters the WP login screen to have a two (2) stage login with data from the server selected and or entered by the user displayed to the user to prove they are talking to your server, and not a spoofed site.  (This means a hacker can't Spoof or Phish your site).

SpoofProof hooks into the WordPress engine to filter out Javascript and SQL injection from posts to your site.

SpoofProof tracks login attampts and stops Brute Force attacks by imposing waiting periods on rapid login attempts.

SpoofProof Detects Man In the Middle (MItM) attacks in progress, records the IP address of the hacker(s) in your log, and stops the user from revealing their password to the hacker(s).  (This stops a hacker from using a MItM to get around the two stage login)

== Installation ==

Download the latest zip file from our site, Upload the SpoofProof plugin to your blog, Activate it, check settings.

1, 2, 3, 4: You're done!




(Or you can just click install at the bottom right of this screen and activate it...)

== Changelog ==

= 1.0.0.2 =
* Released 11/13/2015<BR>
* Fixed bugs with Brute Force detection.
* Fixed bugs in MITM detection service that mis reported some attacks.
* Fixed interface bugs which stripped colors from text that were for emphasis.
* Altered the way internal paths were read from WordPress to support more installations.
standard directories.\n

= 1.0.0.1 =
* Released 11/13/2015
* Fixed bugs with Brute Force detection.
* Fixed bugs in MITM detection service that mis reported some attacks.
* Fixed interface bugs which stripped colors from text that were for emphasis.
* Altered the way internal paths were red from WordPress to support installations in non standard directories.


= 1.0.0.1 =
* Released 11/5/2015
* Added features to the Account screen.
* Fixed bugs in the reporting of some events to the database.
* Adding a version number to the zip file caused some issues with the correct location of image files. Ver 1.0.0.1 has no version number in the zip files.

= 1.0.0.0 =
* Release 10/10/2015