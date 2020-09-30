<?php

/*
This is a cronjob file!
This file is used to delete stored videos after 24 hours automatically when a cron job is set
*/

$video_dir = "user_videos"; // folder location where videos are downloaded
$delete_after = 86400; // time in seconds after videos should be deleted

function endsWithCheck($needle, $haystack) {
     return preg_match('/' . preg_quote($needle, '/') . '$/', $haystack);
 }

if (file_exists($video_dir)) {
	$videos = scandir($video_dir);
	foreach ($videos as $idx => $video_name) {
		if ($video_name == "index.php")
		{
			continue;
		}
		if(!endsWithCheck(".mp4", $video_name))
		{
			continue;
		}
		$create_time = filemtime($video_dir . "/" . $video_name);
		if ((time() - $create_time) > $delete_after)
		{
			unlink($video_dir . "/" . $video_name);
		}
	}
}
