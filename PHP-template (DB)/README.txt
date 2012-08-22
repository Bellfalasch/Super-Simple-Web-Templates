Basic PHP-file structure with basic files and functions.

Content:
* Basic HTML, what you need to get started, nothing more.
* Some extra coding like IE8-style, cache-less css and js, etc
* Basic best-practice usage, like css-loading before js


0.2 new:
* Included database-handling in _databases.php
* _styles in main directory (the only css-file in a project)


Folder structure:
   gfx: graphical effects like icons, flags, etc. Things belonging to the main design used over and over (mostly png:s)
images: all photos used on the site, bigger images, mostly jpg:s
    js: directory for JavaScript-files. Main file is global.js, additional files for different plugins (used sparingly)
  root: i keep the mostly used files in the root, but prefix them with "_" if they are not a actual stand-alone page (like with header and footer-files)
	You could just put them in a "inc"-folder if you'd prefer that, but I don't =P The prefix is for a better overview of the file structure.

Suggested extra:
   css: if more then the _styles.css will be used it's suggested to create a css-directory for all of these