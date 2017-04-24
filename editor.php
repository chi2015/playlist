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

	}

	include('config.php');
	include('func_db.php');
	include('func_view.php');
	include('func_read_playlist.php');

	$link = mysql_connect($host, $user, $pass)
        or die("Could not connect : " . mysql_error());

    mysql_select_db($dbname) or die("Could not select database");

	$load = isset($_POST['load']) ? $_POST['load'] : '';

	if ($load=='yes')
	{
		if (!is_dir("uploads")) mkdir("uploads");

		$loaded_playlists = array();
		$prepared_playlists = array();
		$dates_arr = array();

		for ($i=0; $i<sizeof($_FILES['dataFiles']['name']); $i++)
		{
			 $dataFile = "uploads/".basename($_FILES['dataFiles']['name'][$i]);
			 move_uploaded_file($_FILES['dataFiles']['tmp_name'][$i], $dataFile);
			 $loaded_playlists[] = loadPlaylistFromFile($dataFile);
			 $prepared_playlists[] = preparePlaylistToInsert($loaded_playlists[$i]);
			 $dates_arr[] = $loaded_playlists[$i]["pl_date"];

			 unlink($dataFile);

			 if (!insert_playlist($prepared_playlists[$i], $dates_arr[$i]))
			 {
				header('location: editor.php?added=fail&date='.$dates_arr[$i]);
				die;
			 }

		}

		header('location: editor.php?added=ok&dates='.implode(';',$dates_arr));
  		die;
	}

	echo "<html><head>
		<meta http-equiv=\"Content-Language\" content=\"en-us\">
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
		<link rel=\"icon\" href=\"http://chi2013.h19.ru/play2.png\" type=\"image/png\"/>
		<script type=\"text/javascript\" src=\"js/main.js\"></script>
		<title>Add playlist</title>
		</head><body link=\"#FFFF00\" vlink=\"#00FF00\" alink=\"#00FFFF\" text=\"#FFFFFF\" bgcolor=\"#800000\">
<font face=\"Verdana\" size=\"2\">";

	echo "<table border=\"0\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"138\" valign=\"top\" align=\"left\">
		<font face=\"Verdana\" size=\"2\">
		".getLeftMainPart()."
		</font>
		</td>
		<td align=\"left\" valign=\"top\">";

		if (isset($_GET['added']))
		{
		if ($_GET['added'] == 'ok')
	    {
	    	echo "Playlists added successfully. Dates:<br/>";

	    	if (isset($_GET['dates']))
	    	{
	    		$dates_arr = explode(";",$_GET['dates']);
	    		foreach($dates_arr as $date)
	    		{
	    			echo "<a href=\"archive.php?date=$date\">$date</a><br/>";
	    		}
	    	}
	    }

	    if ($_GET['added'] == 'fail')
	    {
	    	echo "Fail to insert playlist.";

	    	if (isset($_GET['date']))
	    	{
	    		echo "Date: ".$_GET['date'];
	    	}
	    }


		}

		echo "<br/><form action=\"editor.php\" method=\"POST\" enctype=\"multipart/form-data\">
	<font face=\"Verdana\" size=\"2\">
	Insert Playlists From
	<label>Files: <input type=\"file\" name=\"dataFiles[]\" multiple/></label>
	<input type=\"hidden\" name=\"load\" value=\"yes\">
	<input type=\"submit\" value=\"Load\">
	</form></font></body></html>";

?>
