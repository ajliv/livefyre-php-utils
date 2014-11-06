<?php

namespace Livefyre\Utils;


use Livefyre\Core\Core;
use Livefyre\Core\Network;
use Livefyre\Core\Site;

class LivefyreUtils {
    public static function getNetworkFromCore(Core $core) {
        if (get_class($core) == Network::getClassName()) {
            return $core;
        } elseif (get_class($core) == Site::getClassName()) {
            return $core->getNetwork();
        } else {
            return $core->getSite()->getNetwork();
        }
    }

    public static function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}