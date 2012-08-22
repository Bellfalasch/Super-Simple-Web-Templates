!!! You must use this admin on a project based on one of my other simple PHP templates !!!

This is meant to be used on top of my other PHP-templates. It's super easy and small. You just set up your own SQL's for the admin in
_database.php and then you're good to go. The footer and header's are used on each file. The header detects which file you're surfing
and updates the menu according to that.

Each php-file is it's own admin function. Basically they display an empty form to the left with information, and to the right a list of
every data that already are in the database. Click a link there to load this data in the form and perform a database update, or just fill
in an empty form to perform a database insert. This is built to be easy up and go but with the ability for everything to be easily
tailormade, hence no automation of things.

Check the included examples for best practice of how to set your admin files up.


This admin is based on Bootstrap by Twitter.

Just drop the folder "_admin" in your project and extract these files in it. Of course you can edit the folder name after your hearts
content, but at the moment you have to manually update all the files to the new path.


!!! You must use this admin on a project based on one of my other simple PHP templates !!!



File structure:
_database.php - Just SQL, this file uses the main folders database-file and all of it's functions.
_header.php - Start of each template in the admin.
_footer.php - The last few bits of html etc for the admin.
admin.css - The menu is taken from Bootstrap web, also other styles that is needed for the admin is added in this file.
bootstrap.min.css - The projects uses Bootstrap 2.0, check their site for more info: http://twitter.github.com/bootstrap/scaffolding.html



Files:
index.php - just redirects you to login
login.php - validates your login, and also handles log out
users.php - create, delete, and edit users

Example files just so you can see how I have set up different files in my live projects:
datespan.php
overview.php
search.php