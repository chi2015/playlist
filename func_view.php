<?php

function getLeftMainPart()
{
	$year = date("Y") - 1;

	return "<br/><br/>

		<a href=\"index.php\">Current Playlist</a></br>
		<a href=\"index.php?last=yes\">Latest Playlist</a></br>
		<a href=\"archive.php\">Playlists Archive</a></br>
		</br>
		<b>Charts:</b></br>
		<a href=\"top100.php?year=$year\">Top 100 $year</a></br>
		<a href=\"top10artists.php?year=$year\">Top 10 Artists $year</a></br>
		</br>
		<b>Admin:</b></br>
		<a href=\"editor.php\">Add Playlist</a></br>
		<a href=\"edit_list.php\">Edit Playlists</a></br>";
}

function viewPlaylistFromArray($playlist_arr)
{
}


?>
