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
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" href="http://chi2013.h19.ru/play2.png" type="image/png"/>
<title>Playlist Editing</title>
</head>

<body link="#FFFF00" vlink="#00FF00" alink="#00FFFF" text="#FFFFFF" bgcolor="#800000">
<font face="Verdana" size="2">

<?php
	include('config.php');
	include('func_db.php');
	include('func_view.php');

	$link = mysql_connect($host, $user, $pass)
        or die("Could not connect : " . mysql_error());

    mysql_select_db($dbname) or die("Could not select database");

	$act = $_GET['act'];
	if ($act=="del")
	{
		$dates = $_POST['dates'];

		$num_cb = count($dates);
		for ($i=0; $i<$num_cb; $i++)
		{
			$cb = $_POST['cb'.$i];
			if (isset($cb)) delete_playlist($dates[$i]);
		}
	}

		echo "<table border=\"0\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"138\" valign=\"top\" align=\"left\">
		<font face=\"Verdana\" size=\"2\">
		".getLeftMainPart()."
		</font>
		</td>
		<td align=\"left\" valign=\"top\">";

	$query_select_dates = "SELECT DISTINCT pl_date FROM playlist ORDER BY pl_date DESC;";

	$result_select_dates = mysql_query($query_select_dates);
	if (!$result_select_dates)
	{
		echo "Select query failed: ".mysql_error();
		exit;
	}
	if (mysql_num_rows($result_select_dates)==0) echo "You have no any playlist</p>";
	else echo "Choose date for editing or check if want to delete</p>";

	$line_dates;

	echo "<form method=\"POST\" action=\"edit_list.php?act=del\">

	<font face=\"Verdana\" size=\"2\">";
	$i = 0;

	while ($line_dates = mysql_fetch_row($result_select_dates))
	{
		echo "<input type=\"checkbox\" name=\"cb$i\">
		<a href=\"archive.php?date=$line_dates[0]\">$line_dates[0]</a>
		<input type=\"hidden\" name=\"dates[$i]\" value=\"$line_dates[0]\"></br>";
		$i++;
	}

echo "<p><input type=\"submit\" value=\"Delete\" name=\"deleteButton\"></p>
 </form>";
mysql_close($link);
echo "</td>
	</tr>
	</table>";

?>
</font>
</body>

</html>
