<?php

namespace App\Utils;

class UserAgentParser
{
  private $osArray = [
    '/windows nt 10.0/i' => 'Windows 10/11',
    '/windows nt 6.3/i' => 'Windows 8.1',
    '/windows nt 6.2/i' => 'Windows 8',
    '/windows nt 6.1/i' => 'Windows 7',
    '/windows nt 6.0/i' => 'Windows Vista',
    '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
    '/windows nt 5.1/i' => 'Windows XP',
    '/windows xp/i' => 'Windows XP',
    '/windows nt 5.0/i' => 'Windows 2000',
    '/windows me/i' => 'Windows ME',
    '/win98/i' => 'Windows 98',
    '/win95/i' => 'Windows 95',
    '/win16/i' => 'Windows 3.11',
    '/macintosh|mac os x/i' => 'Mac OS X',
    '/mac_powerpc/i' => 'Mac OS 9',
    '/android/i' => 'Android',
    '/iphone/i' => 'iPhone',
    '/ipod/i' => 'iPod',
    '/ipad/i' => 'iPad',
    '/blackberry/i' => 'BlackBerry',
    '/webos/i' => 'Mobile',
    '/ubuntu/i' => 'Ubuntu',
    '/linux/i' => 'Linux'
  ];

  public function getOperatingSystem(string $userAgent): string
  {
    $osPlatform = "Unknown";

    foreach ($this->osArray as $regex => $value) {
      if (preg_match($regex, $userAgent)) {
        $osPlatform = $value;
        break;
      }
    }

    return $osPlatform;
  }

  public function getBrowser(string $userAgent): string
  {
    // Check for Edge (new Chromium-based Edge)
    if (strpos($userAgent, 'Edg/') !== false || strpos($userAgent, 'EdgA/') !== false) {
      return 'Microsoft Edge';
    }

    // Check for old Edge
    if (strpos($userAgent, 'Edge/') !== false) {
      return 'Microsoft Edge (Legacy)';
    }

    // Check for Internet Explorer
    if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
      return 'Internet Explorer';
    }

    // Check for Opera (must be before Chrome check)
    if (strpos($userAgent, 'Opera Mini') !== false) {
      return 'Opera Mini';
    } elseif (strpos($userAgent, 'OPR/') !== false || strpos($userAgent, 'Opera/') !== false) {
      return 'Opera';
    }

    // Check for Firefox (must be before Chrome check)
    if (strpos($userAgent, 'Firefox/') !== false) {
      return 'Mozilla Firefox';
    }

    // Check for Chrome (but not Edge)
    if (strpos($userAgent, 'Chrome/') !== false && strpos($userAgent, 'Edg/') === false) {
      return 'Google Chrome';
    }

    // Check for Safari (but not Chrome-based browsers)
    if (strpos($userAgent, 'Safari/') !== false && strpos($userAgent, 'Chrome/') === false) {
      return 'Safari';
    }

    // Check for mobile browsers
    if (strpos($userAgent, 'SamsungBrowser/') !== false) {
      return 'Samsung Internet';
    }

    if (strpos($userAgent, 'UCBrowser/') !== false) {
      return 'UC Browser';
    }

    if (strpos($userAgent, 'YaBrowser/') !== false) {
      return 'Yandex Browser';
    }

    if (strpos($userAgent, 'Vivaldi/') !== false) {
      return 'Vivaldi';
    }

    if (strpos($userAgent, 'Brave/') !== false) {
      return 'Brave';
    }

    return 'Other';
  }
}
