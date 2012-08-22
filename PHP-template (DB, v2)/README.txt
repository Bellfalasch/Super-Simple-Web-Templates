Version 2 of the Basic PHP-file structure with basic files and functions.
This one adds more extra stuff that you very often anyway need to add to each project, but without getting to big or hard to shrink/edit.

Content:
* Basic HTML, what you need to get started, nothing more.
* Some extra coding like IE8-style, cache-less css and js, etc.
* Basic best-practice usage, like css-loading before js.
* Support for localhost-dev and automatic recognition when files are put online via variables (must be used to have effect ofc).
* Not full, but as much in-code help as possible (remove before deployment).


0.5 Loads of new things:
* Started using /inc/-folder
* Database-file is in inc-folder
* Loads of updates to the database handling
* More example syntaxes for SQL (in the database-file)
* Introducing functions-include
* Introducing globals-include
* Introducing easy error-handling
* Introducing transaction database support (with a manually called start and stop-function making it opptional)
* Easy dev-mode
* Placeholder favicon via code!



Files:
  /inc/globals.php - This file is included from the header-file, but you can also include it from pages that you don't want any design on. This file does the include of the database and functions file, and it starts up error-handling, sets headers, etc.
 /inc/database.php - Login-info for database and overhead functions to communicate with the database so that you just have to write your SQL just as normal, check the examples included!
/inc/functions.php - Basic functions for common tasks I've come over. A basic error-handling system is set up here, with output.
      /_styles.css - Basically just a reset in it's current form, just add your style after.



Folder structure:
   gfx - graphical effects like icons, flags, etc. Things belonging to the main design used over and over (mostly png:s)
images - all photos used on the site, bigger images, mostly jpg:s
   inc - folder for none-design files that is included in the project, like classes etc. Also minor design-files like "sidebar" or "nav" could be included her so that the main folder only containts header and footer as includes (for easy access).
    js - directory for JavaScript-files. Main file is global.js, additional files for different plugins (used sparingly)
  root - i keep the mostly used files in the root, but prefix them with "_" if they are not a actual stand-alone page (like with header and footer-files)
	 You could just put them in a "inc"-folder if you'd prefer that, but I don't =P The prefix is for a better overview of the file structure.

Suggested extras:
   css - if more then the _styles.css will be used it's suggested to create a css-directory for all of these