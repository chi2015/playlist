<?php
session_start();

if (isset($_GET['logout']))
{
	    unset($_SESSION["login"]);
}

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SESSION["login"])) {
   	 header('WWW-Authenticate: Basic realm="Playlist Project"');
   	 header('HTTP/1.0 401 Unauthorized');
   	 echo 'Authentification needed';
    	 $_SESSION["login"] = 1;
    	exit;
	}

	if ($_SERVER['PHP_AUTH_USER']!='chi' || $_SERVER['PHP_AUTH_PW']!='6660913SeRj!')
	{

		header('WWW-Authenticate: Basic realm="Playlist Project"');
   	 	header('HTTP/1.0 403 Forbidden');
   	 	echo 'You have no rights to view this page';
        unset($_SESSION["login"]);

    	exit;

	} ?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" href="http://chi2016.ru/play2.png" type="image/png"/>
</head>
<?php

	$title = "Current Playlist";
	$last = $_GET['last'];
	if ($last=='yes') $title = "Latest Playlist";
	echo "<title>$title</title>";
?>

<body link="#FFFF00" vlink="#00FF00" alink="#00FFFF" text="#FFFFFF" bgcolor="#800000">
<font face="Verdana" size="2">
<?php

	include('config.php');
	include('func_view.php');
	include('func_db.php');

	$link = mysql_connect($host, $user, $pass)
        or die("Could not connect : " . mysql_error());

    mysql_select_db($dbname) or die("Could not select database");


    echo "<table border=\"0\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"138\" valign=\"top\" align=\"left\">
		<font face=\"Verdana\" size=\"2\">
		".getLeftMainPart()."
		</font>
		</td>
		<td align=\"left\" valign=\"top\">
		<font face=\"Verdana\" size=\"2\">";

    $query_select_date;
    $latest = isset($_GET['last']) && $_GET['last'] == 'yes';
	$last_date = get_last_date($latest);
	
	if (!$last_date)
	{
		echo "You have no current or any playlist</p>";
		
	}
	else show_playlist($last_date);
	
    echo "</font></td>
	</tr>
	</table>";
    mysql_close($link);

?>

</font>
</body>
</html>
