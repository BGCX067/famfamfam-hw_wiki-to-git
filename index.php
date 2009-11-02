<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>famfamfam</title>
        <script type="text/javascript" src="/javascript/jquery-1.2.6.pack.js"></script>
        <script type="text/javascript">
//<![CDATA[
$().ready( function() {
    $('#filter')
        .bind('keyup', function() {
            var filtervalue = $(this).val();
            $('div')
                .hide()
                .filter('[id*='+filtervalue+']')
                    .show();
        });


});
//]]>
        </script>
    </head>
    <body>
<?php
$files = glob("*.png");
//$files = array_slice($files, 0, 50);
$basenames = array();
foreach ($files AS $filename) {
	$basenames[] = basename($filename, '.png');
}
$total = count($basenames);
$percolumn = ceil($total / 7);

$cnt = 0;

echo "<input type='text' id='filter' />";

echo "<table cellpadding='0' cellspacing='0' border='0'>\n";
echo   "<tr>\n";
echo     "<td valign='top'>\n";
foreach ($basenames AS $filename) {
	if (++$cnt % $percolumn == 0) {
		echo "</td>\n<td valign='top'>";
	}
	echo  "<div id='{$filename}'><img src='{$filename}.png' alt='{$filename}' />{$filename}</div>\n";
}
echo     "</td>\n";
echo   "</tr>\n";
echo "</table>\n";
?>
    </body>
</html>