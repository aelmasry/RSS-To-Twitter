<?php
/**
 * RSS to Twitter v0.1
 * @author Ali Elsayed <ali.elmasery@gmail.com>
*/

include_once 'parse.php';

$feedUrl = 'http://www.cairoportal.com/News/latestnewsrss'; //the feed you want to micro-syndicate

$rss = new lastRSS;

if ($rs = $rss->get($feedUrl)) {
	$title = $rs['items'][0]['title'];
    $description = $rs['items'][0]['description'];
    $url = $rs['items'][0]['link'];
} else { die('Error: RSS file not found, dude.'); }

$tiny_url =  curlGet("http://tinyurl.com/api-create.php?url=" . $url);
$status = $title . " " . $tiny_url;

// $status = $title . " " . $tiny_url." ".$description;
$status  = smartShorten($status);
// echo $status; //just for status if you are directly viewing the script

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', 'OhmeHEjM5jRiTMWHjvNYl8SB5');
define('CONSUMER_SECRET', '89tgL0mffoANUl6D84liDerGQk2zadjy6Fa4ko5SercO9IUbwQ');
$access_token = '2552637594-hqQDvxJk2DP1cwPuAo9sZulkOsuG48XP02kLLUt';
$access_token_secret = 'yQY2xxvVGZNY0VF9T5z3UTEDP0XVK485TafQ02t82mmJn';

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");

$statues = $connection->post("statuses/update", ["status" => $status]);

/**
 * trim till last space before 140 characters
 * @param string $str the string to shorten
 * @param int $length (optional) the max string length to return
 * @return string the shortened string
 */
function smartShorten($str, $length = 140) 
{
	if (strlen($str) > $length) {
        if (false === ($pos = strrpos($str, ' ', $length))) {
			// no space found; cut till $length
            return $str = mb_substr($str, 0, $length, 'UTF-8').'...';
        }
		
        return $str =  mb_substr($str, 0, $length, 'UTF-8').'...';
    }
	
    return $str;
}