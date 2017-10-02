<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';
include_once 'cgi/linkHandler.class.php';
?>
<html>
<head>
<title>Test page</title>
</head>
<body>
<pre>
<?php
//var_dump(Folder::getAllParents());die();
//var_dump(Folder::getSubFoldersAndCounts(3));die();
$folders = Folder::getFoldersArray();
var_dump($folders);
?>
</pre>

<span id="testSpan"></span>
</body>
</html>