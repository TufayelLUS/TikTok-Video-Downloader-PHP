# TikTok Video Downloader PHP Script
A simple but effective one page TikTok video downloader script in PHP, developed by Tufayel Ahmed
# Screenshot
<img src="https://raw.githubusercontent.com/TufayelLUS/TikTok-Video-Downloader-PHP/master/Screenshot.PNG" alt="Interface" /><br>
# Features
* Supports short url(subject to change), normal video urls(desktop style urls only)
* Supports watermark free video downloading (thanks to <b>@dutchtom91</b> for the API suggestion)
# Video Download Guide
* First find an acceptable URL format for video to download.
* Paste it in the input field of the webpage
* Press download button
* Scroll down to the Download video button. There will be both watermarked and watermarked free download button
* In case the watermark free button loads a blank page, please keep refreshing that blank page again until you see the watermark free video. (API glitch)
* Enjoy the video!
# Acceptable URL Formats
* https://www.tiktok.com/@username/video/videoID
* https://vm.tiktok.com/videoID (or similar short URLs)<br>
<b>N.B: </b> Any URL that is not under the tiktok.com domain is not supported and will throw an error<br>
Do not put the URL of m.tiktok.com as this is not redirected to the web version automatically and you are requested to collect either short URLs or desktop URLs
# Limitations
* May stop working for a while if the user agent is flagged for abuse. Change the user agent if stops working.
* Sometimes it may throw a captcha and the downloader might fail at that time. Try later in such cases.
* Short URL support is subject to change at any time
* Does not support private videos
* No support for video URLs of format m.tiktok.com/v/123456789.htm but supports short URLs and full desktop URLs
# Usage
Simply download and deploy. No changes required.
<br>
Live: Not Available Anymore due to no sponsorships. 
# Troubles Downloading Videos?
* Create an issue in this repository and I will get to know.
* Make sure to follow correct video URL format.
* Try once again and wait for the "Download Video" button to appear in the bottom section of the page. It should auto-scroll for you, if not, scroll down manually.
* Please do not create issues about "Watermark free videos not downloading after videos uploaded from and after 27th July 2020" as there is already an open issue for that. As soon as I have a feasible solution, I will update the code mentioning the changes.
