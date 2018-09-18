<?php

namespace App\Lib;

class Requester {

    /**
     * 单个接口
     * @param $request['url']
     * @param $request['post_data']
     * @param $request['header_data']
     * @param $request['method']
     */
    public static function Curl($request) {
        $method = [
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'OPTIONS',
            'LINK',
            'UNLINK',
            'LOCK',
            'PROPFIND',
            'VIEW',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //禁止直接显示获取的内容 重要
        curl_setopt($ch, CURLOPT_HTTPHEADER, isset($request['header_data']) ? $request['header_data'] : ["Content-Type: application/json; charset=utf-8"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request['method']);
        if (in_array($request['method'], $method)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request['post_data']));
        }

        $resp = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [json_decode($resp, 1), $status];
    }

    /**
     * 并发接口
     * @param $requests['logo']
     * @param $requests['url']
     * @param $requests['post_data']
     * @param $requests['header_data']
     * @param $requests['method']
     */
    public static function RollingCurl($requests) {
        $queue = curl_multi_init();
        $map = [];
        $method = [
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'OPTIONS',
            'LINK',
            'UNLINK',
            'LOCK',
            'PROPFIND',
            'VIEW',
        ];

        foreach ($requests as $request) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $request['url']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, isset($request['header_data']) ? $request['header_data'] : ["Content-Type: application/json; charset=utf-8"]);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request['method']);
            if (in_array($request['method'], $method)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request['post_data']));
            }
            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $request['logo'];
        }

        $responses = [];
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM);

            if ($code != CURLM_OK) {break;}

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($queue)) {

                // get the info and content returned on the request
                $info = curl_getinfo($done['handle']);
                $error = curl_error($done['handle']);
                $results = $this->callback(curl_multi_getcontent($done['handle']));
                $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');

                // remove the curl handle that just completed
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }

        } while ($active);

        curl_multi_close($queue);
        return $responses;
    }

    //回调函数
    public function callback($data) {
        preg_match_all('/<h3>(.+)<\/h3>/iU', $data, $matches);
        return compact('data', 'matches');
    }

    //生成签名
    public static function generateMKII($request_data = [], $request_time = "", $secret_key = "") {
        return strtoupper(md5(sha1(base64_encode(urlencode($secret_key . static::serialize($request_data) . $secret_key . $request_time)))));
    }

    //序列化
    public static function serialize($data) {
        if (is_array($data)) {
            ksort($data);
            $str = "";
            foreach ($data as $key => $value) {
                $str = sprintf('%s%s%s', $str, $key, static::serialize($value));
            }
            return $str;
        } else {
            return $data;
        }
    }
}

/*
$meta = [
'source' => 'cgi',
'version' => 'v1.0',
'secret_key' => 'ABC',
'request_id' => 'A123',
];

$requests = [
[
'logo' => 'a',
'url' => 'http://localhost/study/curl/servera.php',
'header_data' => [
'Content-Type: application/json; charset=utf-8',
"source:{$meta['source']}",
"version:{$meta['version']}",
"secret-key:{$meta['secret_key']}",
"request-id:{$meta['request_id']}"
],
'post_data' => [
'name' => 'kobe',
'age' => 11,
'height' => '198cm',
'weight' => '105kg'
]
],
[
'logo' => 'b',
'url' => 'http://localhost/study/curl/serverb.php',
'post_data' => [
'name' => 'curry',
'age' => 13,
'height' => '190cm',
'weight' => '95kg'
]
]
];
$client = new client();
$result = $client->RollingCurl($requests);
var_dump($result);

$servera = $result['a']['results']['data'];
$serverb = $result['b']['results']['data'];

var_dump( json_decode( $servera ) );
var_dump( json_decode( $serverb ) );

var_dump($_SERVER);
 */
