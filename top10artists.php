<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php
	$title = "Top 10 Artists";
	$year = $_GET['date'];
	if (isset($date)) $title = "Top 10 Artists: $year";
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
    $year = $_GET['year'];

    echo "<table border=\"0\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"138\" valign=\"top\" align=\"left\">
		<font face=\"Verdana\" size=\"2\">
		".getLeftMainPart()."
		</font>
		</td>
		<td align=\"left\" valign=\"top\">
		<font face=\"Verdana\" size=\"2\">";

    if (!isset($year))
    {

    	$query_select_year = "SELECT DISTINCT YEAR(MAX(pl_date)) FROM playlist";
		$result_select_year = mysql_query($query_select_year);
		if (!$result_select_year)
		{
			echo "Select query failed: ".mysql_error();
			exit;
		}
		if (mysql_num_rows($result_select_year)==0)
		{
			echo "You have no playlists</p>";
			exit;
		}

		else
		{
			$line_year = mysql_fetch_row($result_select_year);
			$year = $line_year[0];
		}
   	 }

    	top10artists($year);


    echo "</font></td>
	</tr>
	</table>";
    mysql_close($link);

?>

</font>
</body>
</html>