<?php

include_once('func_date.php');

function insert_playlist($songs_array, $pl_date)
{ 
	$query_check_exist = "SELECT COUNT(pl_date) FROM playlist WHERE pl_date='$pl_date'";
	$result_check_exist = mysql_query($query_check_exist);
	$count = mysql_fetch_row($result_check_exist);
	if ($count[0]>0)
	{
		echo "Error: Playlist dated $pl_date is already exists!";
		return false;
	}

	$num_songs = count($songs_array);

	for ($i=0; $i<$num_songs; $i++)
	{	
		if (($songs_array[$i]["title"]=="") || ($songs_array[$i]["artist"]==""))
		{
			echo "Error: At least one of artists or songs is empty";
			return false;
		}

		for ($j=0; $j<$i; $j++)
			if (($songs_array[$i]["title"]==$songs_array[$j]["title"]) && ($songs_array[$i]["artist"]==$songs_array[$j]["artist"]))
			{
				echo "Error: playlist contains 2 or more the same songs!";
				return false;
			}
	}

	for ($i=0; $i<$num_songs; $i++)
	{
		$songs_array[$i]["title"] = htmlspecialchars($songs_array[$i]["title"], ENT_QUOTES);
		$songs_array[$i]["artist"] = htmlspecialchars($songs_array[$i]["artist"], ENT_QUOTES);


		$current_title = $songs_array[$i]["title"];
		$current_artist = $songs_array[$i]["artist"];

		$query_select_id = "SELECT id FROM songs WHERE title = '$current_title' AND artist = '$current_artist'";
			$result_select_id = mysql_query($query_select_id);
		if (!$result_select_id)
		{
			echo "Invalid query $query_select_id ".mysql_error()."</p>";
			return false;
		}

		if (mysql_num_rows($result_select_id)==0)
		{
			$query_insert_new_song = "INSERT INTO songs (artist, title, date_appear) VALUES('$current_artist','$current_title', '$pl_date')";
			$result_insert_new_song = mysql_query($query_insert_new_song);
			if (!$result_insert_new_song)
			{
				echo "Invalid query $query_insert_new_song ".mysql_error()."</p>";
				return false;
			}

			mysql_free_result($result_select_id);
			$result_select_id = mysql_query($query_select_id);
			if (!$result_select_id)
			{
				echo "Invalid query $query_select_id ".mysql_error()."</p>";
				return false;
			}


		}

		$line = mysql_fetch_row($result_select_id);
		$song_id = $line[0];

		$current_is_new = $songs_array[$i]["is_new"];

		if ($current_is_new == 1)
		{
			$query_update_new_song = "UPDATE songs SET date_appear = '$pl_date' WHERE id = $song_id";
			$result_update_new_song = mysql_query($query_update_new_song);
			if (!$query_update_new_song)
			{
				echo "Invalid query $query_update_new_song ".mysql_error()."</p>";
				return false;
			}
		}

		$current_score = $songs_array[$i]["score"];
		$query_insert_playlist = "INSERT INTO playlist (song_id, pl_date, score) VALUES ('$song_id','$pl_date','$current_score')";
		$result_insert_pl = mysql_query($query_insert_playlist);
		if (!$result_insert_pl)
		{
			echo "Insert query failed $query_insert_playlist: ".mysql_error();
			return false;
		}
	}

	return true;
}

function delete_playlist($pl_date)
{
	$query_delete = "DELETE FROM playlist WHERE pl_date='$pl_date'";
	$result_delete = mysql_query($query_delete);
	if (!$result_delete)
	{
		echo "Delete query failed: ".mysql_error();
		return false;
	}

	$query_delete_song = "DELETE FROM songs WHERE id NOT IN (SELECT DISTINCT song_id FROM playlist)";

			$result_delete_song = mysql_query($query_delete_song);
			if (!$result_delete_song)
			{
				echo "Delete song query failed: ".mysql_error();
				return false;
			}

	return true;
}

function get_last_date($latest = false)
{
	$query_select_date = $latest ? "SELECT MAX(pl_date) FROM playlist" :
								   "SELECT DISTINCT pl_date
									FROM playlist
									WHERE pl_date
									BETWEEN DATE_SUB( NOW( ) , INTERVAL 7 DAY )
									AND NOW( )";

	$result_select_date = mysql_query($query_select_date);
	if (!$result_select_date)
	{
		echo "Select query failed: ".mysql_error();
		return false;
	}
	
	if (mysql_num_rows($result_select_date)==0)
	{
		echo "You have no current or any playlist</p>";
		exit;
	}

	$last_date = mysql_fetch_row($result_select_date);
	return $last_date[0];
}

function show_playlist($pl_date)
{
	    $query_pl_songs = "SELECT songs.artist, songs.title, songs.date_appear
		FROM songs
		LEFT JOIN playlist ON songs.id = playlist.song_id
		WHERE playlist.pl_date = '$pl_date'
		ORDER BY playlist.score DESC , songs.artist ASC";

		$result_pl_songs = mysql_query($query_pl_songs) or
		die ("Select pl_songs query failed ".mysql_error);

		$line_pl_songs;
		$i = 0;

		$pl_date_prev = strftime("%Y-%m-%d", strtotime($pl_date) - 7*24*3600);
		$pl_date_next = strftime("%Y-%m-%d", strtotime($pl_date) + 7*24*3600);

		$query_pl_next = "SELECT 1 FROM playlist WHERE playlist.pl_date = '$pl_date_next'";

		$result_pl_next = mysql_query($query_pl_next) or
		die ("Select pl_next query failed ".mysql_error);

		$next_date_str = '';

		if ($line_next_pl = mysql_fetch_assoc($result_pl_next))
		{
			$next_date_str = "<a href=\"archive.php?date=$pl_date_next\"> next >>></a>";
		}

		$query_pl_prev = "SELECT 1 FROM playlist WHERE playlist.pl_date = '$pl_date_prev'";

		$result_pl_prev = mysql_query($query_pl_prev) or
		die ("Select pl_next query failed ".mysql_error);

		$prev_date_str = '';

		if ($line_prev_pl = mysql_fetch_assoc($result_pl_prev))
		{
			$prev_date_str = "<a href=\"archive.php?date=$pl_date_prev\"><<< prev</a>";
		}

		echo "$prev_date_str $next_date_str </br></br><b>Playlist Sv-Studio<br/> Updated ".formatDate($pl_date)."</b> </br></br>";
		while ($line_pl_songs = mysql_fetch_assoc($result_pl_songs))
		{
			if ($i==0) echo "<b>A-List</b></br></br>";
			if ($i==9) echo "</br><b>B-List</b></br></br>";
			if ($i==19) echo "</br><b>C-List</b></br></br>";
			if ($line_pl_songs["date_appear"]==$pl_date) echo "*";
			echo $line_pl_songs["artist"]." - '".$line_pl_songs["title"]."'</br>";
			$i++;
		}
		echo "</br>* - denotes a new addition</br>";
}

function show_all()
{
	$years = get_years_db();
	
	if (!is_array($years) || count($years) < 1)
	{
		echo "You have no playlists</p>";
		return;
	}
	
	echo "<b>Choose playlist date to view</b></p>";
			
	foreach ($years as $year)
	{
		echo "<b>$year</br></br>";
		show_dates($year);
		echo "</br>";
	}
}

function get_years_db()
{
	$query = "SELECT DISTINCT (YEAR( pl_date )) AS pl_year FROM playlist ORDER BY pl_year DESC";
	$res = mysql_query($query);
	if (!$res)
	{
		echo "Select query failed: ".mysql_error();
		return array();
	}
	
	$ret = array();
	
	while ($line = mysql_fetch_assoc($res))
		if ($line['pl_year'] > 0) $ret[] = $line['pl_year'];
		
	return $ret;
}

function show_years()
{
	echo "<b>Choose playlist year to view</b></p>";
	$years = get_years_db();
	foreach ($years as $year)
		echo '<a href="archive.php?year='.$year.'">'.$year.'</a><br/>';
}

function show_months($year)
{
	$years = get_years_db();
	if ($year > 0 && in_array($year, $years))
	{
		echo "<p><b>$year</b></p>";
		echo "<b>Choose playlist month to view</b></p>";
		$months_str = getMonthStrs();
		foreach ($months_str as $key => $value)
			echo '<a href="archive.php?year='.$year.'&month='.$value.'">'.$key.'</a><br/>';
	}
	else
	{
		echo '<p>You have no any playlist in choosed year</p>';
	}
}

function show_pl_month($year, $month)
{  
	$query = "SELECT DISTINCT(pl_date) FROM playlist WHERE YEAR( pl_date ) =  '$year' AND MONTH( pl_date ) =  '$month' ORDER BY pl_date";
	$res = mysql_query($query);
	if (!$res)
	{
		echo "Select query failed: ".mysql_error();
		return;
	}
	
	echo "<p><b>$year</b></p>";
		echo "<b>Choose playlist to view</b></p>";
	
	while ($line = mysql_fetch_row($res))
	{
		echo "
		<a href=\"archive.php?date=$line[0]\">".formatDate($line[0])."</a></br>";
	}
}

function show_dates($year)
{
	$query_select_dates = "SELECT DISTINCT pl_date FROM playlist WHERE pl_date BETWEEN '$year-01-01' AND '$year-12-31' ORDER BY pl_date DESC";
		$result_select_dates = mysql_query($query_select_dates);
		if (!$result_select_dates)
		{
			echo "Select query failed: ".mysql_error();
			return false;
		}

		$line_dates;

		while ($line_dates = mysql_fetch_row($result_select_dates))
		{
			echo "
			<a href=\"archive.php?date=$line_dates[0]\">".formatDate($line_dates[0])."</a></br>";
		}
		return true;
}

function top100($year, $simple = false, $reverse = false)
{
	$query_select_100 = "SELECT songs.artist AS artist, songs.title AS title, SUM( playlist.score ) + (
SELECT bonus
FROM bonuses
WHERE max_date < date_add( MAX( playlist.pl_date ) , INTERVAL 6
DAY )
ORDER BY max_date DESC
LIMIT 1 ) AS total,
MAX(playlist.score) AS max_score
FROM songs
INNER JOIN playlist ON playlist.song_id = songs.id
WHERE playlist.pl_date
BETWEEN '$year-01-01'
AND '$year-12-31'
GROUP BY songs.id
ORDER BY total DESC , artist ASC
LIMIT 100";
	$res_sel_100 = mysql_query($query_select_100);
	if (!$res_sel_100)
	{
			echo "Select query failed: ".mysql_error();
			return false;
	}
	if (!$simple)
	{
		echo "<p align=\"center\"><font size=\"+1\"> Top 100: $year</font></p>
		<table border=\"1\" width=\"100%\" id=\"table1\">

		<tr>
			<td width=\"4\"></td>
			<td>Artist - 'Title'</td>
			<td>Total</td>
			<!--<td></td>-->
		</tr>";
	}
	else
	{
		echo "<p align=\"center\"><font size=\"+1\"> Top 100: $year</font></p>";

	}


	$line_dates;

	$top100_arr = array();
	
	while ($line_100 = mysql_fetch_assoc($res_sel_100))
	{
		$top100_arr[] = array('artist' => $line_100["artist"], 
							  'title' => $line_100["title"],
		                      'total' => $line_100["total"],
		                      'max_score' => $line_100["max_score"]);
	}
	
	//print_r($top100_arr);
//	return true;
	
	for ($j=1; $j<=100; $j++)
	{ 
		$i = $reverse ? 101-$j : $j;
		
		if (!$simple)
		{
			echo "
			<tr>
				<td width=\"4\" align=\"center\"><b>$i</b></td>
				<td><b>{$top100_arr[$i-1]["artist"]} - '{$top100_arr[$i-1]["title"]}'</b></td>
				<td><b>{$top100_arr[$i-1]["total"]}</b></td>
				<!--<td><b>{$top100_arr[$i-1]["max_score"]}</b></td>-->
			</tr>";
		}
		else
		{
			echo "$i. {$top100_arr[$i-1]["artist"]} - '{$top100_arr[$i-1]["title"]}'</br>";
			}
	}
	
		if (!$simple) echo "</table>";
		return true;

}

function top10artists($year)
{


$query_select_10 = "SELECT artist, SUM(total) AS artist_total, COUNT(title) AS songs FROM (SELECT songs.artist AS artist, songs.title AS title, SUM( playlist.score ) + (
SELECT bonus
FROM bonuses
WHERE max_date < date_add( MAX( playlist.pl_date ) , INTERVAL 6
DAY )
ORDER BY max_date DESC
LIMIT 1 ) AS total
FROM songs
INNER JOIN playlist ON playlist.song_id = songs.id
WHERE playlist.pl_date
BETWEEN '$year-01-01'
AND '$year-12-31'
GROUP BY songs.id
ORDER BY total DESC , artist ASC
) top100 GROUP BY top100.artist ORDER BY artist_total DESC LIMIT 10
";
	$res_sel_10 = mysql_query($query_select_10);
	if (!$res_sel_10)
	{
			echo "Select query failed: ".mysql_error();
			return false;
	}
	echo "<p align=\"center\"><font size=\"+1\"> Top 10 Artists: $year</font></p>
	<table border=\"1\" width=\"100%\" id=\"table1\">

	<tr>
		<td width=\"4\"></td>
		<td>Artist</td>
		<td>Total</td>
		<td>Songs</td>
	</tr>";


	$line_dates;
	$i = 1;
		while ($line_10 = mysql_fetch_assoc($res_sel_10))
		{

			echo "
	<tr>
		<td width=\"4\" align=\"center\"><b>$i</b></td>
		<td><b>{$line_10["artist"]}</b></td>
		<td><b>{$line_10["artist_total"]}</b></td>
		<td><b>{$line_10["songs"]}</b></td>
	</tr>";
			$i++;
		}

		echo "</table>";
		return true;
}

?>
