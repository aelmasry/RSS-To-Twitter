<?php
/**
 * RSS to Twitter v0.1
 * @author Ali Elsayed <ali.elmasery@gmail.com>
*/

include_once 'parse.php';

$feedUrl = ''; //the feed you want to micro-syndicate

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

define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
$access_token = '';
$access_token_secret = '';

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