# LinkBox
storing user's urls in one place for using everywhere

after copying files into some folder on hosting mashine one has to set proper constants for runnig site in settings file linkbox\cgi\settings.inc.php
main settings are:
a) full path to database file, looks like
define(DBPATHSQLITE,'/srv/disk/userfolder/www/yourdomain.name/linkbox/assets/db/linkbox_bs.db3'); or whatewer your database file name is.
b) root dir for site's files, looks like
define(SITE_DIR, '/srv/disk/userfolder/www/yourdomain.name/linkbox');

these values one can find from hoster's docs or trying to require non-existing file from siteroot - in the error message proper path will be presented. For example, run from root web folder php file <? require 'blah.z'; ?> and you got error like
Warning: require(blah.z) [function.require]: failed to open stream: No such file or directory in /srv/disk/userfolder/www/yourdomain.name/linkbox/blah.php on line 1
