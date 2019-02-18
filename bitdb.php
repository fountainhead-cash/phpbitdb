<?php

$BITDB_URL     = 'https://bitdb.fountainhead.cash/q/';
$BITSOCKET_URL = 'https://bitsocket.fountainhead.cash/s/';

function query_bitdb($query) {
    global $BITDB_URL;

    $json_string = json_encode($query);
    $url = $BITDB_URL . base64_encode($json_string);

    return json_decode(file_get_contents($url));
}

function query_bitsocket($query, $fn) {
    global $BITSOCKET_URL;

    $json_string = json_encode($query);
    $url = $BITSOCKET_URL . base64_encode($json_string);

    $callback = function($ch, $data) use ($fn) {
        $bytes = strlen($data);
        static $buf = '';
        $buf .= $data;

        while(true) {
            $pos = strpos($buf, "\n");
            if($pos === false) {
                break;
            }

            $data = substr($buf, 0, $pos+1);
            $buf  = substr($buf, $pos+1);

            // comment
            if (substr($data, 0, 1) == ":") {
                break;
            } else if (substr($data, 0, 6) == "data: ") {
                $fn(json_decode(substr($data, 6)));
            }
        }

        return $bytes;
    };

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
    curl_exec($ch);
    curl_close($ch);
}
