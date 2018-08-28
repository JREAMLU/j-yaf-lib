<?php

namespace App\Lib;

class Common {

    public function ip2region($ip) {
        $info = \Ip2region::btreeSearchString($ip);
        $region = explode('|', $info['region']);

        return [
            'city_id' => $info['city_id'],
            'country' => $region[0] == '0' ? '-' : $region[0],
            'area' => $region[1] == '0' ? '-' : $region[1],
            'province' => $region[2] == '0' ? '-' : $region[2],
            'city' => $region[3] == '0' ? '-' : $region[3],
            'isp' => $region[4] == '0' ? '-' : $region[4],
        ];
    }
}