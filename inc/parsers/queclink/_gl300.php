<?php


function parse_gl300($data) {
    if (in_array(explode(":", $data[0])[0], array("+RESP", "+BUFF"))) {
        if (in_array(explode(":", $data[0])[1], array("GTFRI", "GTPNL", "GTNMR", "GTRTL", "GTDOG"))) {
            $uri = parse_gl300_fri($data);
        } elseif (explode(":", $data[0])[1] == "GTINF") {
            $uri = parse_gl300_inf($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTPFA", "GTPNA"))) {
            $uri = parse_gl300_pfa($data);
        } else {
            $uri = NULL;
        }
    } else {
        $uri = NULL;
    }
    file_get_contents('https://s1.recoverylocate.com/insert.php' . $uri);
    // file_get_contents('https://s3.recoverylocate.com/insert.php' . $uri);
    file_get_contents('https://d1.recoverylocate.com/insert.php' . $uri);
}

function parse_gl300_fri($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $name = $params[3];
    $report_id = $params[4];
    $report_type = $params[5];
    if (($type == 'NMR') && ($report_type == 00)) {
        $type = 'NMR0';
    } elseif (($type == 'NMR') && ($report_type == 01)) {
        $type = 'NMR1';
    }
    $hdop = $params[7];
    switch ($hdop) {
        case '1':
            $gps_level = '100';
            break;
        case '2':
            $gps_level = '75';
            break;
        case '3':
            $gps_level = '50';
            break;
        case '4':
            $gps_level = '37';
            break;
        case '5':
            $gps_level = '12';
            break;
        default:
            $gps_level = '0';
            break;
    }
    $speed = number_format(($params[8] * 0.53996), 1, '.', ',');
    $heading = $params[9];
    $altitude = number_format($params[10], 1, '.', ',');
    $longitude = $params[11];
    $latitude = $params[12];
    $fixTime = strtotime($params[13]) * 1000;
    $mcc = $params[14];
    $mnc = $params[15];
    switch (true) {
        case strstr($mnc, '0260'):
            $network = 'T-Mobile';
            break;
        case strstr($mnc, '0410'):
            $network = 'ATT';
            break;
        case strstr($mnc, '0480');
            $network = 'Verizon';
            break;
        default:
            $network = $mnc;
    }
    $batterylevel = $params[19];
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'reportid' => $report_id,
        'reporttype' => $report_type,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'network' => $network,
        'batterylevel' => $batterylevel,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gl300_inf($params) {
    $imei = $params[2];
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[23]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'name' => $params[3],
        'iccid' => $params[5],
        'rssi' => $params[6],
        'charger' => $params[8],
        'battery' => $params[11],
        'batterylevel' => $params[18],
        'temp1' => number_format($params[20], 1),
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gl300_pfa($params) {
    $imei = $params[2];
    $name = $params[3];
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[4]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'name' => $name,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}
