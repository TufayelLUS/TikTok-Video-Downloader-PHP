<?php

$store_locally = true; /* change to false if you don't want to host videos locally */ 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function downloadVideo($video_url, $geturl = false)
{
    $ch = curl_init();
    $headers = array(
        'Range: bytes=0-',
    );
    $options = array(
        CURLOPT_URL            => $video_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_USERAGENT => 'okhttp',
        CURLOPT_ENCODING       => "utf-8",
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_COOKIEJAR      => 'cookie.txt',
	CURLOPT_COOKIEFILE     => 'cookie.txt',
        CURLOPT_REFERER        => 'https://www.tiktok.com/',
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_MAXREDIRS      => 10,
    );
    curl_setopt_array( $ch, $options );
    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($geturl === true)
    {
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }
    curl_close($ch);
    $filename = "user_videos/" . generateRandomString() . ".mp4";
    $d = fopen($filename, "w");
    fwrite($d, $data);
    fclose($d);
    return $filename;
}

if (isset($_GET['url']) && !empty($_GET['url'])) {
    if ($_SERVER['HTTP_REFERER'] != "") {
        $url = $_GET['url'];
        $name = downloadVideo($url);
        echo $name;
        exit();
    }
    else
    {
        echo "";
        exit();
    }
}

function getContent($url, $geturl = false)
  {
    $ch = curl_init();
    $options = array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Mobile Safari/537.36',
        CURLOPT_ENCODING       => "utf-8",
        CURLOPT_AUTOREFERER    => false,
        CURLOPT_COOKIEJAR      => 'cookie.txt',
	CURLOPT_COOKIEFILE     => 'cookie.txt',
        CURLOPT_REFERER        => 'https://www.tiktok.com/',
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_MAXREDIRS      => 10,
    );
    curl_setopt_array( $ch, $options );
    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($geturl === true)
    {
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }
    curl_close($ch);
    return strval($data);
  }

  function getKey($playable)
  {
  	$ch = curl_init();
  	$headers = [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: en-US,en;q=0.9',
    'Range: bytes=0-200000'
	];

    $options = array(
        CURLOPT_URL            => $playable,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
        CURLOPT_ENCODING       => "utf-8",
        CURLOPT_AUTOREFERER    => false,
        CURLOPT_COOKIEJAR      => 'cookie.txt',
	CURLOPT_COOKIEFILE     => 'cookie.txt',
        CURLOPT_REFERER        => 'https://www.tiktok.com/',
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_MAXREDIRS      => 10,
    );
    curl_setopt_array( $ch, $options );
    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $tmp = explode("vid:", $data);
    if(count($tmp) > 1){
    	$key = trim(explode("%", $tmp[1])[0]);
    }
    else
    {
    	$key = "";
    }
    return $key;
  }
?>

<!DOCTYPE html>
<html>
<head>
	<title>TikTok Video Downloader</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Gotu&display=swap" rel="stylesheet">
<style type="text/css">
	html, body
	{
		font-family: "Gotu"
	}
	input
	{
		padding: 5px;
		border-radius: 10px;
		border-style: solid;
		border-color: blue;
		transition-duration: 0.5s;
		width: 80%;
	}
	input:focus
	{
		border-color: skyblue;
		transition-duration: 0.5s;
	}
</style>
</head>
<body class="bg-light">
	<div class="text-center p-5">
		<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjlweCIgaGVpZ2h0PSIzMnB4IiB2aWV3Qm94PSIwIDAgMjkgMzIiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDU1LjIgKDc4MTgxKSAtIGh0dHBzOi8vc2tldGNoYXBwLmNvbSAtLT4KICAgIDx0aXRsZT7nvJbnu4QgMjwvdGl0bGU+CiAgICA8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4KICAgIDxnIGlkPSLpobXpnaIxIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0i57yW57uELTIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuOTc5MjM2LCAwLjAwMDAwMCkiIGZpbGwtcnVsZT0ibm9uemVybyI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0xMC43OTA3NjQ1LDEyLjMzIEwxMC43OTA3NjQ1LDExLjExIEMxMC4zNjcyNjI5LDExLjA0Mjg4ODcgOS45Mzk1MDY3NCwxMS4wMDYxMjg0IDkuNTEwNzY0NDgsMTAuOTk5OTc4NiBDNS4zNTk5NjU0OSwxMC45OTEyMjI4IDEuNjg1MDk2NzksMTMuNjgxMDIwNSAwLjQzODY2NzY5NCwxNy42NDAyNjU4IEMtMC44MDc3NjEzOTksMjEuNTk5NTExMiAwLjY2MzUwNTg0MiwyNS45MDkzODg3IDQuMDcwNzY0NDgsMjguMjggQzEuNTE4NDg0ODQsMjUuNTQ4NDgxNiAwLjgwOTc5OTU0NSwyMS41NzIwODM0IDIuMjYxMjY4MTcsMTguMTI3MDA1MyBDMy43MTI3MzY3OSwxNC42ODE5MjczIDcuMDUzMjk1NDUsMTIuNDExNTQyOCAxMC43OTA3NjQ1LDEyLjMzIEwxMC43OTA3NjQ1LDEyLjMzIFoiIGlkPSLot6/lvoQiIGZpbGw9IiMyNUY0RUUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTExLjAyMDc2NDUsMjYuMTUgQzEzLjM0MTUyODcsMjYuMTQ2ODc3NiAxNS4yNDkxNjYyLDI0LjMxODU0MTQgMTUuMzUwNzY0NSwyMiBMMTUuMzUwNzY0NSwxLjMxIEwxOS4xMzA3NjQ1LDEuMzEgQzE5LjA1MzYwNjgsMC44Nzc2ODIzMjIgMTkuMDE2NzgxOCwwLjQzOTEzMDk5MiAxOS4wMjA3NjQ1LDAgTDEzLjg1MDc2NDUsMCBMMTMuODUwNzY0NSwyMC42NyBDMTMuNzY0Nzk4LDIzLjAwMDMzODggMTEuODUyNjg1MywyNC44NDYyMTIgOS41MjA3NjQ0OCwyNC44NSBDOC44MjM5MDkxNCwyNC44NDQwNjcgOC4xMzg0Mjg4NCwyNC42NzI2OTY5IDcuNTIwNzY0NDgsMjQuMzUgQzguMzMyNjgyNDUsMjUuNDc0OTE1NCA5LjYzMzQ2MjAzLDI2LjE0Mzg4NzggMTEuMDIwNzY0NSwyNi4xNSBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMjVGNEVFIj48L3BhdGg+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yNi4xOTA3NjQ1LDguMzMgTDI2LjE5MDc2NDUsNy4xOCBDMjQuNzk5NjQsNy4xODA0NzYyNSAyMy40MzkzNzgxLDYuNzY5OTYyNDIgMjIuMjgwNzY0NSw2IEMyMy4yOTY0NDQ2LDcuMTgwNzE3NjkgMjQuNjY4OTYyMiw3Ljk5ODYxMTc3IDI2LjE5MDc2NDUsOC4zMyBMMjYuMTkwNzY0NSw4LjMzIFoiIGlkPSLot6/lvoQiIGZpbGw9IiMyNUY0RUUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTIyLjI4MDc2NDUsNiBDMjEuMTM5NDY3NSw0LjcwMDMzMTYxIDIwLjUxMDI5NjcsMy4wMjk2NTIxNiAyMC41MTA3NjQ1LDEuMyBMMTkuMTMwNzY0NSwxLjMgQzE5LjQ5MDk4MTIsMy4yMzI2ODUxOSAyMC42MzAwMzgzLDQuOTMyMjMwNjcgMjIuMjgwNzY0NSw2IEwyMi4yODA3NjQ1LDYgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNOS41MTA3NjQ0OCwxNi4xNyBDNy41MTkyMTgxNCwxNi4xODAyMTc4IDUuNzkwMjE2MjYsMTcuNTQ0NTkzIDUuMzE3MjEyMDEsMTkuNDc5MTgwMyBDNC44NDQyMDc3NywyMS40MTM3Njc3IDUuNzQ4NjA5NTYsMjMuNDIyMDA2OSA3LjUxMDc2NDQ4LDI0LjM1IEM2LjU1NTk0ODM0LDIzLjAzMTc3MTggNi40MjEwNjg3MSwyMS4yODk0MzM2IDcuMTYxNjI4ODMsMTkuODM5OTYxMyBDNy45MDIxODg5NiwxOC4zOTA0ODg5IDkuMzkzMDY3MzQsMTcuNDc4Nzc4MiAxMS4wMjA3NjQ1LDE3LjQ4IEMxMS40NTQ3NzUyLDE3LjQ4NTQwODQgMTEuODg1NzkwOCwxNy41NTI3NTQ2IDEyLjMwMDc2NDUsMTcuNjggTDEyLjMwMDc2NDUsMTIuNDIgQzExLjg3Njk5MTksMTIuMzU2NTA1NiAxMS40NDkyNTYyLDEyLjMyMzA4ODcgMTEuMDIwNzY0NSwxMi4zMiBMMTAuNzkwNzY0NSwxMi4zMiBMMTAuNzkwNzY0NSwxNi4zMiBDMTAuMzczNjM2OCwxNi4yMDgxNTQ0IDkuOTQyNDQ5MzQsMTYuMTU3NjI0NiA5LjUxMDc2NDQ4LDE2LjE3IFoiIGlkPSLot6/lvoQiIGZpbGw9IiNGRTJDNTUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTI2LjE5MDc2NDUsOC4zMyBMMjYuMTkwNzY0NSwxMi4zMyBDMjMuNjE1NDcsMTIuMzI1MDE5MyAyMS4xMDcwMjUsMTEuNTA5ODYyMiAxOS4wMjA3NjQ1LDEwIEwxOS4wMjA3NjQ1LDIwLjUxIEMxOS4wMDk3MzUyLDI1Ljc1NDQxNTggMTQuNzU1MTkxOSwzMC4wMDAwMTE2IDkuNTEwNzY0NDgsMzAgQzcuNTYzMTI3ODQsMzAuMDAzNDU1NiA1LjY2MjQwMzIxLDI5LjQwMjQ5MTIgNC4wNzA3NjQ0OCwyOC4yOCBDNi43MjY5ODY3NCwzMS4xMzY4MTA4IDEwLjg2MDgyNTcsMzIuMDc3MTk4OSAxNC40OTE0NzA2LDMwLjY1MDU1ODYgQzE4LjEyMjExNTUsMjkuMjIzOTE4MyAyMC41MDk5Mzc1LDI1LjcyMDg4MjUgMjAuNTEwNzY0NSwyMS44MiBMMjAuNTEwNzY0NSwxMS4zNCBDMjIuNjA0MDI0LDEyLjgzOTk2NjMgMjUuMTE1NTcyNCwxMy42NDQ1MDEzIDI3LjY5MDc2NDUsMTMuNjQgTDI3LjY5MDc2NDUsOC40OSBDMjcuMTg2NTkyNSw4LjQ4ODM5NTM1IDI2LjY4MzkzMTMsOC40MzQ3NzgxNiAyNi4xOTA3NjQ1LDguMzMgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNMTkuMDIwNzY0NSwyMC41MSBMMTkuMDIwNzY0NSwxMCBDMjEuMTEzNDA4NywxMS41MDExODk4IDIzLjYyNTM2MjMsMTIuMzA1ODU0NiAyNi4yMDA3NjQ1LDEyLjMgTDI2LjIwMDc2NDUsOC4zIEMyNC42NzkyNTQyLDcuOTc4NzEyNjUgMjMuMzAzNDQwMyw3LjE3MTQ3NDkxIDIyLjI4MDc2NDUsNiBDMjAuNjMwMDM4Myw0LjkzMjIzMDY3IDE5LjQ5MDk4MTIsMy4yMzI2ODUxOSAxOS4xMzA3NjQ1LDEuMyBMMTUuMzUwNzY0NSwxLjMgTDE1LjM1MDc2NDUsMjIgQzE1LjI3NTE1MjEsMjMuODQ2NzY2NCAxNC4wMzgxOTkxLDI1LjQ0MzAyMDEgMTIuMjY4NzY5LDI1Ljk3NzIzMDIgQzEwLjQ5OTMzODksMjYuNTExNDQwMyA4LjU4NTcwOTQyLDI1Ljg2NjM4MTUgNy41MDA3NjQ0OCwyNC4zNyBDNS43Mzg2MDk1NiwyMy40NDIwMDY5IDQuODM0MjA3NzcsMjEuNDMzNzY3NyA1LjMwNzIxMjAxLDE5LjQ5OTE4MDMgQzUuNzgwMjE2MjYsMTcuNTY0NTkzIDcuNTA5MjE4MTQsMTYuMjAwMjE3OCA5LjUwMDc2NDQ4LDE2LjE5IEM5LjkzNDkwMywxNi4xOTM4NjkzIDEwLjM2NjEzODYsMTYuMjYxMjQ5OSAxMC43ODA3NjQ1LDE2LjM5IEwxMC43ODA3NjQ1LDEyLjM5IEM3LjAyMjMzNzksMTIuNDUzNjY5MSAzLjY1NjUzOTI5LDE0LjczMTk3NjggMi4yMDA5NDU2MSwxOC4xOTc2NzYxIEMwLjc0NTM1MTkzOCwyMS42NjMzNzUzIDEuNDc0OTQ0OTMsMjUuNjYxNzQ3NiA0LjA2MDc2NDQ4LDI4LjM5IEM1LjY2ODA5NTQyLDI5LjQ3NTUwNjMgNy41NzE1ODc4MiwzMC4wMzc4MjI0IDkuNTEwNzY0NDgsMzAgQzE0Ljc1NTE5MTksMzAuMDAwMDExNiAxOS4wMDk3MzUyLDI1Ljc1NDQxNTggMTkuMDIwNzY0NSwyMC41MSBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMDAwMDAwIj48L3BhdGg+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4="> <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iOTdweCIgaGVpZ2h0PSIyMnB4IiB2aWV3Qm94PSIwIDAgOTcgMjIiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDU1LjIgKDc4MTgxKSAtIGh0dHBzOi8vc2tldGNoYXBwLmNvbSAtLT4KICAgIDx0aXRsZT7nvJbnu4Q8L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZyBpZD0i6aG16Z2iMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Iue8lue7hCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC43NzAwMDAsIDAuMjgwMDAwKSIgZmlsbC1ydWxlPSJub256ZXJvIj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSIzLjU1MjcxMzY4ZS0xNSAwLjA2IDE2LjEyIDAuMDYgMTQuNjQgNC43MiAxMC40NiA0LjcyIDEwLjQ2IDIxLjcyIDUuMjMgMjEuNzIgNS4yMyA0LjcyIDAuMDEgNC43MiI+PC9wb2x5Z29uPgogICAgICAgICAgICA8cG9seWdvbiBpZD0i6Lev5b6EIiBmaWxsPSIjMDAwMDAwIiBwb2ludHM9IjQyLjUyIDAuMDYgNTkuMDEgMC4wNiA1Ny41MyA0LjcyIDUyLjk5IDQuNzIgNTIuOTkgMjEuNzIgNDcuNzcgMjEuNzIgNDcuNzcgNC43MiA0Mi41MyA0LjcyIj48L3BvbHlnb24+CiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSLot6/lvoQiIGZpbGw9IiMwMDAwMDAiIHBvaW50cz0iMTcuMSA2Ljk1IDIyLjI3IDYuOTUgMjIuMjcgMjEuNzIgMTcuMTQgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSIyNC4zMiAwIDI5LjQ4IDAgMjkuNDggMTAuMDkgMzQuNiA1LjA5IDQwLjc2IDUuMDkgMzQuMjkgMTEuMzcgNDEuNTQgMjEuNzIgMzUuODUgMjEuNzIgMzEuMDEgMTQuNTMgMjkuNDggMTYuMDEgMjkuNDggMjEuNzIgMjQuMzIgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSI3OS4wMSAwIDg0LjIzIDAgODQuMjMgMTAuMDkgODkuMzQgNS4wOSA5NS41IDUuMDkgODkuMDMgMTEuMzcgOTYuMjMgMjEuNzIgOTAuNTQgMjEuNzIgODUuNzEgMTQuNTMgODQuMjMgMTYuMDEgODQuMjMgMjEuNzIgNzkuMDYgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPGNpcmNsZSBpZD0i5qSt5ZyG5b2iIiBmaWxsPSIjMDAwMDAwIiBjeD0iMTkuNjkiIGN5PSIyLjY2IiByPSIyLjYiPjwvY2lyY2xlPgogICAgICAgICAgICA8cGF0aCBkPSJNNTguMzUsMTIuODggQzU4LjM1MTU4MTQsOC4yNjY1NzI2OSA2MS45MDA2NDc1LDQuNDMwMDk3NTggNjYuNSw0LjA3IEM2Ni4yNyw0LjA3IDY1Ljk2LDQuMDcgNjUuNzMsNC4wNyBDNjEuMDU1Njk0Niw0LjM0MjY0OTU3IDU3LjQwNDc1NzIsOC4yMTI3NDk1OCA1Ny40MDQ3NTcyLDEyLjg5NSBDNTcuNDA0NzU3MiwxNy41NzcyNTA0IDYxLjA1NTY5NDYsMjEuNDQ3MzUwNCA2NS43MywyMS43MiBDNjUuOTYsMjEuNzIgNjYuMjcsMjEuNzIgNjYuNSwyMS43MiBDNjEuODg5MTMwNywyMS4zNTkwMjIxIDU4LjMzNTg5MTQsMTcuNTA0OTU2NCA1OC4zNSwxMi44OCBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMjVGNEVFIj48L3BhdGg+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik02OC41MSw0LjA0IEM2OC4yNyw0LjA0IDY3Ljk2LDQuMDQgNjcuNzMsNC4wNCBDNzIuMzE0NDYsNC40MTg2NTYzNyA3NS44NDIzMzI1LDguMjQ5OTI4ODkgNzUuODQyMzMyNSwxMi44NSBDNzUuODQyMzMyNSwxNy40NTAwNzExIDcyLjMxNDQ2LDIxLjI4MTM0MzYgNjcuNzMsMjEuNjYgQzY3Ljk2LDIxLjY2IDY4LjI3LDIxLjY2IDY4LjUxLDIxLjY2IEM3My4zOTIxOTcyLDIxLjY2IDc3LjM1LDE3LjcwMjE5NzIgNzcuMzUsMTIuODIgQzc3LjM1LDcuOTM3ODAyODEgNzMuMzkyMTk3MiwzLjk4IDY4LjUxLDMuOTggTDY4LjUxLDQuMDQgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNNjcuMTEsMTcuMTggQzY0LjczNTE3NTYsMTcuMTggNjIuODEsMTUuMjU0ODI0NCA2Mi44MSwxMi44OCBDNjIuODEsMTAuNTA1MTc1NiA2NC43MzUxNzU2LDguNTggNjcuMTEsOC41OCBDNjkuNDg0ODI0NCw4LjU4IDcxLjQxLDEwLjUwNTE3NTYgNzEuNDEsMTIuODggQzcxLjQwNDUwMTYsMTUuMjUyNTQzMiA2OS40ODI1NDMyLDE3LjE3NDUwMTYgNjcuMTEsMTcuMTggTDY3LjExLDE3LjE4IFogTTY3LjExLDQuMDQgQzYyLjIyNzgwMjgsNC4wNCA1OC4yNyw3Ljk5NzgwMjgxIDU4LjI3LDEyLjg4IEM1OC4yNywxNy43NjIxOTcyIDYyLjIyNzgwMjgsMjEuNzIgNjcuMTEsMjEuNzIgQzcxLjk5MjE5NzIsMjEuNzIgNzUuOTUsMTcuNzYyMTk3MiA3NS45NSwxMi44OCBDNzUuOTUsMTAuNTM1NDg2MiA3NS4wMTg2NDU1LDguMjg2OTk3NjQgNzMuMzYwODIzOSw2LjYyOTE3NjA1IEM3MS43MDMwMDI0LDQuOTcxMzU0NDcgNjkuNDU0NTEzOCw0LjA0IDY3LjExLDQuMDQgWiIgaWQ9IuW9oueKtiIgZmlsbD0iIzAwMDAwMCI+PC9wYXRoPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+">
		<h1 class="mt-5">Download <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjlweCIgaGVpZ2h0PSIzMnB4IiB2aWV3Qm94PSIwIDAgMjkgMzIiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDU1LjIgKDc4MTgxKSAtIGh0dHBzOi8vc2tldGNoYXBwLmNvbSAtLT4KICAgIDx0aXRsZT7nvJbnu4QgMjwvdGl0bGU+CiAgICA8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4KICAgIDxnIGlkPSLpobXpnaIxIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0i57yW57uELTIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuOTc5MjM2LCAwLjAwMDAwMCkiIGZpbGwtcnVsZT0ibm9uemVybyI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0xMC43OTA3NjQ1LDEyLjMzIEwxMC43OTA3NjQ1LDExLjExIEMxMC4zNjcyNjI5LDExLjA0Mjg4ODcgOS45Mzk1MDY3NCwxMS4wMDYxMjg0IDkuNTEwNzY0NDgsMTAuOTk5OTc4NiBDNS4zNTk5NjU0OSwxMC45OTEyMjI4IDEuNjg1MDk2NzksMTMuNjgxMDIwNSAwLjQzODY2NzY5NCwxNy42NDAyNjU4IEMtMC44MDc3NjEzOTksMjEuNTk5NTExMiAwLjY2MzUwNTg0MiwyNS45MDkzODg3IDQuMDcwNzY0NDgsMjguMjggQzEuNTE4NDg0ODQsMjUuNTQ4NDgxNiAwLjgwOTc5OTU0NSwyMS41NzIwODM0IDIuMjYxMjY4MTcsMTguMTI3MDA1MyBDMy43MTI3MzY3OSwxNC42ODE5MjczIDcuMDUzMjk1NDUsMTIuNDExNTQyOCAxMC43OTA3NjQ1LDEyLjMzIEwxMC43OTA3NjQ1LDEyLjMzIFoiIGlkPSLot6/lvoQiIGZpbGw9IiMyNUY0RUUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTExLjAyMDc2NDUsMjYuMTUgQzEzLjM0MTUyODcsMjYuMTQ2ODc3NiAxNS4yNDkxNjYyLDI0LjMxODU0MTQgMTUuMzUwNzY0NSwyMiBMMTUuMzUwNzY0NSwxLjMxIEwxOS4xMzA3NjQ1LDEuMzEgQzE5LjA1MzYwNjgsMC44Nzc2ODIzMjIgMTkuMDE2NzgxOCwwLjQzOTEzMDk5MiAxOS4wMjA3NjQ1LDAgTDEzLjg1MDc2NDUsMCBMMTMuODUwNzY0NSwyMC42NyBDMTMuNzY0Nzk4LDIzLjAwMDMzODggMTEuODUyNjg1MywyNC44NDYyMTIgOS41MjA3NjQ0OCwyNC44NSBDOC44MjM5MDkxNCwyNC44NDQwNjcgOC4xMzg0Mjg4NCwyNC42NzI2OTY5IDcuNTIwNzY0NDgsMjQuMzUgQzguMzMyNjgyNDUsMjUuNDc0OTE1NCA5LjYzMzQ2MjAzLDI2LjE0Mzg4NzggMTEuMDIwNzY0NSwyNi4xNSBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMjVGNEVFIj48L3BhdGg+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yNi4xOTA3NjQ1LDguMzMgTDI2LjE5MDc2NDUsNy4xOCBDMjQuNzk5NjQsNy4xODA0NzYyNSAyMy40MzkzNzgxLDYuNzY5OTYyNDIgMjIuMjgwNzY0NSw2IEMyMy4yOTY0NDQ2LDcuMTgwNzE3NjkgMjQuNjY4OTYyMiw3Ljk5ODYxMTc3IDI2LjE5MDc2NDUsOC4zMyBMMjYuMTkwNzY0NSw4LjMzIFoiIGlkPSLot6/lvoQiIGZpbGw9IiMyNUY0RUUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTIyLjI4MDc2NDUsNiBDMjEuMTM5NDY3NSw0LjcwMDMzMTYxIDIwLjUxMDI5NjcsMy4wMjk2NTIxNiAyMC41MTA3NjQ1LDEuMyBMMTkuMTMwNzY0NSwxLjMgQzE5LjQ5MDk4MTIsMy4yMzI2ODUxOSAyMC42MzAwMzgzLDQuOTMyMjMwNjcgMjIuMjgwNzY0NSw2IEwyMi4yODA3NjQ1LDYgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNOS41MTA3NjQ0OCwxNi4xNyBDNy41MTkyMTgxNCwxNi4xODAyMTc4IDUuNzkwMjE2MjYsMTcuNTQ0NTkzIDUuMzE3MjEyMDEsMTkuNDc5MTgwMyBDNC44NDQyMDc3NywyMS40MTM3Njc3IDUuNzQ4NjA5NTYsMjMuNDIyMDA2OSA3LjUxMDc2NDQ4LDI0LjM1IEM2LjU1NTk0ODM0LDIzLjAzMTc3MTggNi40MjEwNjg3MSwyMS4yODk0MzM2IDcuMTYxNjI4ODMsMTkuODM5OTYxMyBDNy45MDIxODg5NiwxOC4zOTA0ODg5IDkuMzkzMDY3MzQsMTcuNDc4Nzc4MiAxMS4wMjA3NjQ1LDE3LjQ4IEMxMS40NTQ3NzUyLDE3LjQ4NTQwODQgMTEuODg1NzkwOCwxNy41NTI3NTQ2IDEyLjMwMDc2NDUsMTcuNjggTDEyLjMwMDc2NDUsMTIuNDIgQzExLjg3Njk5MTksMTIuMzU2NTA1NiAxMS40NDkyNTYyLDEyLjMyMzA4ODcgMTEuMDIwNzY0NSwxMi4zMiBMMTAuNzkwNzY0NSwxMi4zMiBMMTAuNzkwNzY0NSwxNi4zMiBDMTAuMzczNjM2OCwxNi4yMDgxNTQ0IDkuOTQyNDQ5MzQsMTYuMTU3NjI0NiA5LjUxMDc2NDQ4LDE2LjE3IFoiIGlkPSLot6/lvoQiIGZpbGw9IiNGRTJDNTUiPjwvcGF0aD4KICAgICAgICAgICAgPHBhdGggZD0iTTI2LjE5MDc2NDUsOC4zMyBMMjYuMTkwNzY0NSwxMi4zMyBDMjMuNjE1NDcsMTIuMzI1MDE5MyAyMS4xMDcwMjUsMTEuNTA5ODYyMiAxOS4wMjA3NjQ1LDEwIEwxOS4wMjA3NjQ1LDIwLjUxIEMxOS4wMDk3MzUyLDI1Ljc1NDQxNTggMTQuNzU1MTkxOSwzMC4wMDAwMTE2IDkuNTEwNzY0NDgsMzAgQzcuNTYzMTI3ODQsMzAuMDAzNDU1NiA1LjY2MjQwMzIxLDI5LjQwMjQ5MTIgNC4wNzA3NjQ0OCwyOC4yOCBDNi43MjY5ODY3NCwzMS4xMzY4MTA4IDEwLjg2MDgyNTcsMzIuMDc3MTk4OSAxNC40OTE0NzA2LDMwLjY1MDU1ODYgQzE4LjEyMjExNTUsMjkuMjIzOTE4MyAyMC41MDk5Mzc1LDI1LjcyMDg4MjUgMjAuNTEwNzY0NSwyMS44MiBMMjAuNTEwNzY0NSwxMS4zNCBDMjIuNjA0MDI0LDEyLjgzOTk2NjMgMjUuMTE1NTcyNCwxMy42NDQ1MDEzIDI3LjY5MDc2NDUsMTMuNjQgTDI3LjY5MDc2NDUsOC40OSBDMjcuMTg2NTkyNSw4LjQ4ODM5NTM1IDI2LjY4MzkzMTMsOC40MzQ3NzgxNiAyNi4xOTA3NjQ1LDguMzMgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNMTkuMDIwNzY0NSwyMC41MSBMMTkuMDIwNzY0NSwxMCBDMjEuMTEzNDA4NywxMS41MDExODk4IDIzLjYyNTM2MjMsMTIuMzA1ODU0NiAyNi4yMDA3NjQ1LDEyLjMgTDI2LjIwMDc2NDUsOC4zIEMyNC42NzkyNTQyLDcuOTc4NzEyNjUgMjMuMzAzNDQwMyw3LjE3MTQ3NDkxIDIyLjI4MDc2NDUsNiBDMjAuNjMwMDM4Myw0LjkzMjIzMDY3IDE5LjQ5MDk4MTIsMy4yMzI2ODUxOSAxOS4xMzA3NjQ1LDEuMyBMMTUuMzUwNzY0NSwxLjMgTDE1LjM1MDc2NDUsMjIgQzE1LjI3NTE1MjEsMjMuODQ2NzY2NCAxNC4wMzgxOTkxLDI1LjQ0MzAyMDEgMTIuMjY4NzY5LDI1Ljk3NzIzMDIgQzEwLjQ5OTMzODksMjYuNTExNDQwMyA4LjU4NTcwOTQyLDI1Ljg2NjM4MTUgNy41MDA3NjQ0OCwyNC4zNyBDNS43Mzg2MDk1NiwyMy40NDIwMDY5IDQuODM0MjA3NzcsMjEuNDMzNzY3NyA1LjMwNzIxMjAxLDE5LjQ5OTE4MDMgQzUuNzgwMjE2MjYsMTcuNTY0NTkzIDcuNTA5MjE4MTQsMTYuMjAwMjE3OCA5LjUwMDc2NDQ4LDE2LjE5IEM5LjkzNDkwMywxNi4xOTM4NjkzIDEwLjM2NjEzODYsMTYuMjYxMjQ5OSAxMC43ODA3NjQ1LDE2LjM5IEwxMC43ODA3NjQ1LDEyLjM5IEM3LjAyMjMzNzksMTIuNDUzNjY5MSAzLjY1NjUzOTI5LDE0LjczMTk3NjggMi4yMDA5NDU2MSwxOC4xOTc2NzYxIEMwLjc0NTM1MTkzOCwyMS42NjMzNzUzIDEuNDc0OTQ0OTMsMjUuNjYxNzQ3NiA0LjA2MDc2NDQ4LDI4LjM5IEM1LjY2ODA5NTQyLDI5LjQ3NTUwNjMgNy41NzE1ODc4MiwzMC4wMzc4MjI0IDkuNTEwNzY0NDgsMzAgQzE0Ljc1NTE5MTksMzAuMDAwMDExNiAxOS4wMDk3MzUyLDI1Ljc1NDQxNTggMTkuMDIwNzY0NSwyMC41MSBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMDAwMDAwIj48L3BhdGg+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4="> <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iOTdweCIgaGVpZ2h0PSIyMnB4IiB2aWV3Qm94PSIwIDAgOTcgMjIiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDU1LjIgKDc4MTgxKSAtIGh0dHBzOi8vc2tldGNoYXBwLmNvbSAtLT4KICAgIDx0aXRsZT7nvJbnu4Q8L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZyBpZD0i6aG16Z2iMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Iue8lue7hCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC43NzAwMDAsIDAuMjgwMDAwKSIgZmlsbC1ydWxlPSJub256ZXJvIj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSIzLjU1MjcxMzY4ZS0xNSAwLjA2IDE2LjEyIDAuMDYgMTQuNjQgNC43MiAxMC40NiA0LjcyIDEwLjQ2IDIxLjcyIDUuMjMgMjEuNzIgNS4yMyA0LjcyIDAuMDEgNC43MiI+PC9wb2x5Z29uPgogICAgICAgICAgICA8cG9seWdvbiBpZD0i6Lev5b6EIiBmaWxsPSIjMDAwMDAwIiBwb2ludHM9IjQyLjUyIDAuMDYgNTkuMDEgMC4wNiA1Ny41MyA0LjcyIDUyLjk5IDQuNzIgNTIuOTkgMjEuNzIgNDcuNzcgMjEuNzIgNDcuNzcgNC43MiA0Mi41MyA0LjcyIj48L3BvbHlnb24+CiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSLot6/lvoQiIGZpbGw9IiMwMDAwMDAiIHBvaW50cz0iMTcuMSA2Ljk1IDIyLjI3IDYuOTUgMjIuMjcgMjEuNzIgMTcuMTQgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSIyNC4zMiAwIDI5LjQ4IDAgMjkuNDggMTAuMDkgMzQuNiA1LjA5IDQwLjc2IDUuMDkgMzQuMjkgMTEuMzcgNDEuNTQgMjEuNzIgMzUuODUgMjEuNzIgMzEuMDEgMTQuNTMgMjkuNDggMTYuMDEgMjkuNDggMjEuNzIgMjQuMzIgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPHBvbHlnb24gaWQ9Iui3r+W+hCIgZmlsbD0iIzAwMDAwMCIgcG9pbnRzPSI3OS4wMSAwIDg0LjIzIDAgODQuMjMgMTAuMDkgODkuMzQgNS4wOSA5NS41IDUuMDkgODkuMDMgMTEuMzcgOTYuMjMgMjEuNzIgOTAuNTQgMjEuNzIgODUuNzEgMTQuNTMgODQuMjMgMTYuMDEgODQuMjMgMjEuNzIgNzkuMDYgMjEuNzIiPjwvcG9seWdvbj4KICAgICAgICAgICAgPGNpcmNsZSBpZD0i5qSt5ZyG5b2iIiBmaWxsPSIjMDAwMDAwIiBjeD0iMTkuNjkiIGN5PSIyLjY2IiByPSIyLjYiPjwvY2lyY2xlPgogICAgICAgICAgICA8cGF0aCBkPSJNNTguMzUsMTIuODggQzU4LjM1MTU4MTQsOC4yNjY1NzI2OSA2MS45MDA2NDc1LDQuNDMwMDk3NTggNjYuNSw0LjA3IEM2Ni4yNyw0LjA3IDY1Ljk2LDQuMDcgNjUuNzMsNC4wNyBDNjEuMDU1Njk0Niw0LjM0MjY0OTU3IDU3LjQwNDc1NzIsOC4yMTI3NDk1OCA1Ny40MDQ3NTcyLDEyLjg5NSBDNTcuNDA0NzU3MiwxNy41NzcyNTA0IDYxLjA1NTY5NDYsMjEuNDQ3MzUwNCA2NS43MywyMS43MiBDNjUuOTYsMjEuNzIgNjYuMjcsMjEuNzIgNjYuNSwyMS43MiBDNjEuODg5MTMwNywyMS4zNTkwMjIxIDU4LjMzNTg5MTQsMTcuNTA0OTU2NCA1OC4zNSwxMi44OCBaIiBpZD0i6Lev5b6EIiBmaWxsPSIjMjVGNEVFIj48L3BhdGg+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik02OC41MSw0LjA0IEM2OC4yNyw0LjA0IDY3Ljk2LDQuMDQgNjcuNzMsNC4wNCBDNzIuMzE0NDYsNC40MTg2NTYzNyA3NS44NDIzMzI1LDguMjQ5OTI4ODkgNzUuODQyMzMyNSwxMi44NSBDNzUuODQyMzMyNSwxNy40NTAwNzExIDcyLjMxNDQ2LDIxLjI4MTM0MzYgNjcuNzMsMjEuNjYgQzY3Ljk2LDIxLjY2IDY4LjI3LDIxLjY2IDY4LjUxLDIxLjY2IEM3My4zOTIxOTcyLDIxLjY2IDc3LjM1LDE3LjcwMjE5NzIgNzcuMzUsMTIuODIgQzc3LjM1LDcuOTM3ODAyODEgNzMuMzkyMTk3MiwzLjk4IDY4LjUxLDMuOTggTDY4LjUxLDQuMDQgWiIgaWQ9Iui3r+W+hCIgZmlsbD0iI0ZFMkM1NSI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNNjcuMTEsMTcuMTggQzY0LjczNTE3NTYsMTcuMTggNjIuODEsMTUuMjU0ODI0NCA2Mi44MSwxMi44OCBDNjIuODEsMTAuNTA1MTc1NiA2NC43MzUxNzU2LDguNTggNjcuMTEsOC41OCBDNjkuNDg0ODI0NCw4LjU4IDcxLjQxLDEwLjUwNTE3NTYgNzEuNDEsMTIuODggQzcxLjQwNDUwMTYsMTUuMjUyNTQzMiA2OS40ODI1NDMyLDE3LjE3NDUwMTYgNjcuMTEsMTcuMTggTDY3LjExLDE3LjE4IFogTTY3LjExLDQuMDQgQzYyLjIyNzgwMjgsNC4wNCA1OC4yNyw3Ljk5NzgwMjgxIDU4LjI3LDEyLjg4IEM1OC4yNywxNy43NjIxOTcyIDYyLjIyNzgwMjgsMjEuNzIgNjcuMTEsMjEuNzIgQzcxLjk5MjE5NzIsMjEuNzIgNzUuOTUsMTcuNzYyMTk3MiA3NS45NSwxMi44OCBDNzUuOTUsMTAuNTM1NDg2MiA3NS4wMTg2NDU1LDguMjg2OTk3NjQgNzMuMzYwODIzOSw2LjYyOTE3NjA1IEM3MS43MDMwMDI0LDQuOTcxMzU0NDcgNjkuNDU0NTEzOCw0LjA0IDY3LjExLDQuMDQgWiIgaWQ9IuW9oueKtiIgZmlsbD0iIzAwMDAwMCI+PC9wYXRoPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+" alt="TikTok"> video easily!</h1>
		<h4><b>Script last modified:</b> <span style="color:#236c82;font-style:italic"><?php date_default_timezone_set('UTC'); echo date("F d Y H:i:s A", filemtime(__FILE__)); ?> (UTC)</span> <a href="https://github.com/TufayelLUS/TikTok-Video-Downloader-PHP/commits/master" rel="nofollow" title="Click to view commits history in github" target="_blank">Check Logs</a></h4>
		
	</div>
	<div class="text-center">
		Paste a video url below and press "Download". Now scroll down to "Download Video" button or "Download Watermark Free!" button and press to initiate the download process.<br><br>
		<form method="POST" class="mt-2">
			<input type="text" placeholder="https://www.tiktok.com/@username/video/1234567890123456789" class="mb-3" name="tiktok-url"><br><br>
			<button class="btn btn-success" type="submit">Download</button>
		</form>
	</div>
	<?php
		if (isset($_POST['tiktok-url']) && !empty($_POST['tiktok-url'])) {
			$url = trim($_POST['tiktok-url']);
			$resp = getContent($url);
			//echo "$resp";
			$check = explode('"downloadAddr":"', $resp);
			if (count($check) > 1){
				$contentURL = explode("\"",$check[1])[0];
                $contentURL = str_replace("\\u0026", "&", $contentURL);
				$thumb = explode("\"",explode('og:image" content="', $resp)[1])[0];
				$username = explode('/',explode('"$pageUrl":"/@', $resp)[1])[0];
				$create_time = explode(',', explode('"createTime":', $resp)[1])[0];
				$dt = new DateTime("@$create_time");
				$create_time = $dt->format("d M Y H:i:s A");
				$videoKey = getKey($contentURL);
				$cleanVideo = "https://api2-16-h2.musical.ly/aweme/v1/play/?video_id=$videoKey&vr_type=0&is_play_url=1&source=PackSourceEnum_PUBLISH&media_type=4";
				$cleanVideo = getContent($cleanVideo, true);
				if (!file_exists("user_videos") && $store_locally){
					mkdir("user_videos");
				}
				if ($store_locally){
					?>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $('#wmarked_link').text("Please wait ...");
                            $.get('./<?php echo basename($_SERVER['PHP_SELF']); ?>?url=<?php echo urlencode($contentURL); ?>').done(function(data)
                                {
                                    $('#wmarked_link').removeAttr('disabled');
                                    $('#wmarked_link').attr('onclick', 'window.location.href="' + data + '"');
                                    $('#wmarked_link').text("Download Video");
                                });
                        });
                    </script>
                    <?php
				}
		?>
		<script>
		    $(document).ready(function(){
		        $('html, body').animate({
					    scrollTop: ($('#result').offset().top)
					},1000);
		    });
		</script>
	<div class="border m-3 mb-5" id="result">
	    <div class="text-center"><br>Bot/Scraper Development Services: <a target="_blank" href="https://www.we-can-solve.com">We-Can-Solve.com</a></div>
		<div class="row m-0 p-2">
			<div class="col-sm-5 col-md-5 col-lg-5 text-center"><img width="250px" height="250px" src="<?php echo $thumb; ?>"></div>
			<div class="col-sm-6 col-md-6 col-lg-6 text-center mt-5"><ul style="list-style: none;padding: 0px">
				<li>a video by <b>@<?php echo $username; ?></b></li>
				<li>uploaded on <b><?php echo $create_time; ?></b></li>
				<li><button id="wmarked_link" disabled="disabled" class="btn btn-primary mt-3" onclick="window.location.href='<?php if ($store_locally){ echo $filename;} else { echo $contentURL; } ?>'">Download Video</button> <button class="btn btn-info mt-3" onclick="window.location.href='<?php echo $cleanVideo; ?>'">Download Watermark Free!</button></li>
				<li><div class="alert alert-primary mb-0 mt-3">If the video opens directly, try saving it by pressing CTRL+S or on phone, save from three dots in the bottom left corner</div></li>
			</ul></div>
		</div>
	</div>
	<?php
			}
			else
			{
				?>
				<script>
        		    $(document).ready(function(){
        		        $('html, body').animate({
        					    scrollTop: ($('#result').offset().top)
        					},1000);
        		    });
        		</script>
				<div class="mx-5 px-5 my-3" id="result">
				    <div class="text-center"><br>Bot/Scraper Development Services: <a target="_blank" href="https://www.we-can-solve.com">We-Can-Solve.com</a></div>
					<div class="alert alert-danger mb-0"><b>Please double check your url and try again.</b></div>
				</div>

				<?php
			}
		}
	?>
	<div class="m-5">
		&nbsp;
	</div>
	<div class="bg-dark text-white" style="position: fixed; bottom: 0;width: 100%;padding:15px">Developed by <a target="_blank" href="https://www.github.com/TufayelLUS">tufayel.rocks</a> <span style="float: right;">Copyright &copy; <?php echo date("Y"); ?></span></div>
    <script type="text/javascript">
        window.setInterval(function(){
            if ($("input[name='tiktok-url']").attr("placeholder") == "https://www.tiktok.com/@username/video/1234567890123456789") {
                $("input[name='tiktok-url']").attr("placeholder", "https://vm.tiktok.com/a1b2c3/");
            }
            else
            {
                $("input[name='tiktok-url']").attr("placeholder", "https://www.tiktok.com/@username/video/1234567890123456789");
            }
        }, 3000);
    </script>
</body>
</html>
