<?php
/**
 * Created by PhpStorm.
 * User: caio_
 * Date: 11/02/2019
 * Time: 04:52 PM
 */

namespace App;


class Helper {
    static function convertTimeToSec($time) {
        $time = explode(':', $time);

        return $time[0]*3600 + (isset($time[1]) ? $time[1]*60 : 0);
    }

    static function convertSecToTime($seconds) {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }
}