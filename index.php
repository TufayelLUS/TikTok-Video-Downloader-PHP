<?php
// TikTok Downloader — CLI + Web, with cookie caching
// Usage: php index.php <url> [-n]          (CLI)
//        ?url=https://...&nowm=1           (GET)

$cookie_file = __DIR__ . '/_cookie.txt';

function escape_decode($str) {
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function ($m) {
        $cp = isset($m[3]) ? hexdec($m[3]) : ((hexdec($m[1]) << 10) + hexdec($m[2]) + 0x10000 - (0xD800 << 10) - 0xDC00);
        if ($cp > 0xD7FF && 0xE000 > $cp) $cp = 0xFFFD;
        if ($cp < 0x80) return chr($cp);
        if ($cp < 0xA0) return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
        return html_entity_decode('&#' . $cp . ';');
    }, $str);
}

function cookies_fresh($file) {
    if (!file_exists($file)) return false;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $now = time();
    foreach ($lines as $line) {
        if ($line[0] === '#') continue;
        $parts = explode("\t", $line);
        if (count($parts) >= 5 && is_numeric($parts[4]) && (int)$parts[4] > $now)
            return true;
    }
    return false;
}

function curl_get($url, $cookie_file, $headers = []) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => array_merge([
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0 Safari/537.36',
            'Referer: https://www.tiktok.com/',
        ], $headers),
        CURLOPT_COOKIEJAR => $cookie_file,
        CURLOPT_COOKIEFILE => $cookie_file,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['data' => $data, 'http' => $info['http_code'], 'type' => $info['content_type']];
}

function extract_json_str($html, $key) {
    $s = explode("$key\":\"", $html);
    if (count($s) < 2) return null;
    $e = explode('"', $s[1]);
    return escape_decode($e[0]);
}

function parse_page($html) {
    $dl = extract_json_str($html, 'downloadAddr');
    $pl = extract_json_str($html, 'playAddr');
    $thumb = extract_json_str($html, 'dynamicCover') ?? '';
    $user = extract_json_str($html, 'uniqueId') ?? '';
    return [$dl, $pl, $thumb, $user];
}

function get_video($tiktok_url, $cookie_file, $no_watermark = false) {
    $need_refresh = !cookies_fresh($cookie_file);

    if (!$need_refresh) {
        $page = curl_get($tiktok_url, $cookie_file);
        if ($page['http'] === 200) {
            [$dl, $pl, $thumb, $user] = parse_page($page['data']);
            $dl_url = $no_watermark ? $pl : $dl;
            if ($dl_url) {
                $r = curl_get($dl_url, $cookie_file);
                if ($r['http'] === 200 && strlen($r['data']) >= 1000)
                    return [$r['data'], $thumb, $user, null];
            }
        }
        $need_refresh = true;
    }

    if ($need_refresh) {
        $page = curl_get($tiktok_url, $cookie_file);
        if ($page['http'] !== 200) return [null, null, null, "Page fetch failed: HTTP {$page['http']}"];
        [$dl, $pl, $thumb, $user] = parse_page($page['data']);
        $dl_url = $no_watermark ? $pl : $dl;
        if (!$dl_url) return [null, null, null, "Could not find video URL in page"];
        $r = curl_get($dl_url, $cookie_file);
        if ($r['http'] !== 200 || strlen($r['data']) < 1000)
            return [null, null, null, "Download failed: HTTP {$r['http']}, " . strlen($r['data']) . " bytes"];
        return [$r['data'], $thumb, $user, null];
    }

    return [null, null, null, "Unknown error"];
}

function get_video_url($tiktok_url, $cookie_file, $no_watermark) {
    $need_refresh = !cookies_fresh($cookie_file);
    if (!$need_refresh) {
        $page = curl_get($tiktok_url, $cookie_file);
        if ($page['http'] === 200) {
            [$dl, $pl, $thumb, $user] = parse_page($page['data']);
            $u = $no_watermark ? $pl : $dl;
            if ($u) return [$u, $thumb, $user, null];
        }
    }
    $page = curl_get($tiktok_url, $cookie_file);
    if ($page['http'] !== 200) return [null, null, null, "Page fetch failed: HTTP {$page['http']}"];
    [$dl, $pl, $thumb, $user] = parse_page($page['data']);
    $u = $no_watermark ? $pl : $dl;
    if (!$u) return [null, null, null, "Could not find video URL"];
    return [$u, $thumb, $user, null];
}

function stream_video($src_url, $cookie_file) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $src_url,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_FILE => fopen('php://output', 'w'),
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0 Safari/537.36',
            'Referer: https://www.tiktok.com/',
        ],
        CURLOPT_COOKIEFILE => $cookie_file,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function is_cli() {
    return php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_SOFTWARE']);
}

// ─── Proxy mode (no file saved) ─────────────────────────────────────────────
if (!is_cli() && isset($_GET['stream'])) {
    $url = $_GET['url'] ?? '';
    $nowm = isset($_GET['nowm']);
    if (!$url) { http_response_code(400); exit; }
    [$video_url, $thumb, $user, $err] = get_video_url($url, $cookie_file, $nowm);
    if (!$video_url) { http_response_code(500); echo "Error: $err"; exit; }
    preg_match('/@(\w+)/', $url, $u);
    preg_match('/video\/(\d+)/', $url, $v);
    $name = ($u[1] ?? 'tiktok') . '_' . ($v[1] ?? time()) . ($nowm ? '_nowm' : '') . '.mp4';
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Cache-Control: no-cache');
    stream_video($video_url, $cookie_file);
    exit;
}

// ─── CLI ─────────────────────────────────────────────────────────────────────
if (is_cli()) {
    if ($argc < 2) {
        echo "Usage: php index.php <url> [-n]\n  -n   No watermark\n";
        exit(1);
    }
    $url = $argv[1];
    $nowm = in_array('-n', array_slice($argv, 2));
    echo "Fetching...\n";
    [$data, $thumb, $user, $err] = get_video($url, $cookie_file, $nowm);
    if ($err) { echo "Error: $err\n"; exit(1); }
    preg_match('/@(\w+)/', $url, $u);
    preg_match('/video\/(\d+)/', $url, $v);
    $suffix = $nowm ? '_nowm' : '';
    $name = ($u[1] ?? 'tiktok') . '_' . ($v[1] ?? time()) . "$suffix.mp4";
    file_put_contents($name, $data);
    echo "Saved: $name (" . strlen($data) . " bytes)\n";
    exit;
}

// ─── Web ─────────────────────────────────────────────────────────────────────
$url = $_REQUEST['url'] ?? $_POST['tiktok-url'] ?? '';
$error = '';
$thumb = '';
$username = '';
$url_enc = '';

if ($url) {
    $url_enc = urlencode($url);
    // Just get the metadata (thumb, username) — no file saved
    [$video_url, $thumb, $username, $err] = get_video_url($url, $cookie_file, false);
    if ($err) $error = $err;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>TikTok Downloader</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:sans-serif;background:#f5f5f5;display:flex;flex-direction:column;align-items:center;min-height:100vh;padding:40px 20px}h1{font-size:28px;margin-bottom:8px;color:#111}h2{color:#666;font-weight:400;font-size:16px;margin-bottom:30px}.box{background:#fff;border-radius:12px;padding:30px;width:100%;max-width:500px;box-shadow:0 2px 12px rgba(0,0,0,.08)}input[type=text]{width:100%;padding:12px 16px;border:2px solid #ddd;border-radius:8px;font-size:15px;transition:border .2s}input:focus{outline:none;border-color:#fe2c55}button{width:100%;margin-top:12px;padding:12px;background:#fe2c55;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer}button:hover{background:#d92145}.result{margin-top:20px;text-align:center}.result img{border-radius:8px;max-width:240px;margin-bottom:12px}.result a{display:inline-block;padding:10px 24px;background:#25f4ee;color:#000;text-decoration:none;border-radius:8px;font-weight:600;margin:8px 4px 0}.result a.nowm{background:#fe2c55;color:#fff}.result a.nowm:hover{background:#d92145}.error{background:#fff0f0;color:#c00;padding:12px 16px;border-radius:8px;margin-top:20px}footer{margin-top:40px;color:#888;font-size:13px}</style>
</head>
<body>
<h1>TikTok Downloader</h1>
<h2>Paste a video URL and download</h2>
<div class="box">
<form method="post">
<input type="text" name="tiktok-url" placeholder="https://www.tiktok.com/@user/video/1234567890" value="<?= htmlspecialchars($url) ?>" required>
<button type="submit">Download</button>
</form>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($url_enc): ?>
<div class="result">
<?php if ($thumb): ?><img src="<?= htmlspecialchars($thumb) ?>"><?php endif; ?>
<?php if ($username): ?><p>@<?= htmlspecialchars($username) ?></p><?php endif; ?>
<a href="?stream=1&url=<?= $url_enc ?>" class="btn">Download (with watermark)</a>
<a href="?stream=1&nowm=1&url=<?= $url_enc ?>" class="btn nowm">Download (no watermark)</a>
<p style="margin-top:8px;font-size:13px;color:#888">Streamed directly — no files stored on server</p>
</div>
<?php endif; ?>
</div>
<footer>Single-file TikTok downloader</footer>
</body>
</html>
