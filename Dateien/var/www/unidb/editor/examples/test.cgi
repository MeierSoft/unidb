#!/usr/bin/perl -w
#
#
#

use CGI;

print "Content-type: text/html\n\n";
$c = new CGI;
$ta = $c->param('ta');

print <<EOF;
<!DOCTYPE html>
<html lang="en">
<body>
<textarea style="width: 100%; height: 200px">$ta</textarea>
$ta
</body>
</html>
EOF
