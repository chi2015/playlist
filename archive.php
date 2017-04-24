<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php
	$title = "Playlist Archive";
	$date = $_GET['date'];
	if (isset($date)) $title = "Playlist Date: $date";
	echo "<title>$title</title>";
?>

<body link="#FFFF00" vlink="#00FF00" alink="#00FFFF" text="#FFFFFF" bgcolor="#800000">
<font face="Verdana" size="2">
<?php
	include('config.php');
	include('func_db.php');
	include('func_view.php');

	$link = mysql_connect($host, $user, $pass)
        or die("Could not connect : " . mysql_error());

    mysql_select_db($dbname) or die("Could not select database");
    $date = $_GET['date'];

    echo "<table border=\"0\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"138\" valign=\"top\" align=\"left\">
		<font face=\"Verdana\" size=\"2\">
		".getLeftMainPart()."
		</font>
		</td>
		<td align=\"left\" valign=\"top\">
		<font face=\"Verdana\" size=\"2\">";

    if (isset($_GET['all']) && ($_GET['all'] == 'yes'))
    {
		show_all();
	}
	else
	if (isset($_GET['year']))
	{
		if (isset($_GET['month']))
			show_pl_month($_GET['year'], $_GET['month']);
		else show_months($_GET['year']);
		exit;
	}
	else
	if (isset($_GET['date']))
	{
		show_playlist($date);
	}
	else 
	{
		show_years();
		echo "<br/><p><a href=\"archive.php?all=yes\">Show all playlists on one page</a></p>";
	}
		
    echo "</font></td>
	</tr>
	</table>";
	
    mysql_close($link);
?>

</font>
</body>
</html>
