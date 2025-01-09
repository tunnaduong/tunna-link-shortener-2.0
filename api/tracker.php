<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/serverconnect.php';

// Get the data from the HTTP request
$data = $_POST;
function getOS()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $osPlatform    =   "Unknown";

    $osArray       =   array(
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );

    foreach ($osArray as $regex => $value) {

        if (preg_match($regex, $userAgent)) {
            $osPlatform    =   $value;
        }
    }

    return $osPlatform;
}

$operating_system = getOS();

// if there is a http referer header, then set the "ref_url" variable to the value of the header else set it to "Unknown"
if (isset($data['ref'])) {
    $ref_url = $data['ref'];
} else {
    $ref_url = "Unknown";
}

// if the page is refreshed, set the ref_url to "Page refreshed"
if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') {
    $ref_url = "Page refreshed";
}

// check for user's browser name
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {
    $browser = 'Internet Explorer';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) { //For Supporting IE 11
    $browser = 'Internet Explorer';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
    $browser = 'Mozilla Firefox';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
    $browser = 'Google Chrome';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE) {
    $browser = "Opera Mini";
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE) {
    $browser = "Opera";
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE) {
    $browser = "Safari";
} else {
    $browser = 'Other';
}

// get user's IP address
function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

$user_ip = getUserIP() == "192.168.1.103" ? "103.81.85.224" : getUserIP();
// $details = json_decode(file_get_contents("http://ip-api.com/json/{$user_ip}"));
if (isset($details->city) && isset($details->country)) {
    $location = "{$details->city}, {$details->country}";
} else {
    $location = "Unknown";
}
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$code = $data['id'];
$size = $data['size'];

$sql = "INSERT INTO tracker (ref_code, ref_url, ip_address, location, screen_size, browser, OS, browser_user_agent) VALUES ('$code', '$ref_url', '$user_ip', '$location', '$size', '$browser', '$operating_system', '$user_agent')";
// insert db
$ver = mysqli_query($conn, $sql);
if (!$ver) {
    echo mysqli_error($conn);
    die();
} else {
    echo "Query succesfully executed!";
}
