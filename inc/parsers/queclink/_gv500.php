<?php


function parse_gv500($data) {
    if (in_array(explode(":", $data[0])[0], array("+RESP", "+BUFF"))) {
        if (explode(":", $data[0])[1] == "GTFRI") {
            $uri = parse_gv500_fri($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTIGL", "GTVGL", "GTRTL", "GTDOG"))) {
            $uri = parse_gv500_loc($data);
        } elseif (explode(":", $data[0])[1] == "GTINF") {
            $uri = parse_gv500_inf($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTPFA", "GTPNA"))) {
            $uri = parse_gv500_pfa($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTVGN", "GTVGF"))) {
            $uri = parse_gv500_ign($data);
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

function parse_gv500_fri($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $vin = $params[3];
    $name = $params[4];
    $vbat = $params[5];
    $report_id = substr($params[6], 0, 1);
    $report_type = substr($params[6], 1);

    $hdop = $params[8];
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
    $speed = number_format(($params[9] * 0.53996), 1, '.', ',');
    $heading = $params[10];
    $altitude = number_format($params[11], 1, '.', ',');
    $longitude = $params[12];
    $latitude = $params[13];
    $fixTime = strtotime($params[14]) * 1000;
    $mcc = $params[15];
    $mnc = $params[16];
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
    $status = substr($params[25], 0, 2);
    if (in_array($status, array("16", "1A", "11", "12", "41", "42"))) {
        $ign = '0';
    }
    if (in_array($status, array("21", "22"))) {
        $ign = '1';
    }
    $rpm = $params[26];
    $fuellevel = $params[28];
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'vin' => $vin,
        'reportid' => $report_id,
        'reporttype' => $report_type,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'network' => $network,
        'status' => $status,
        'ign' => $ign,
        'engingrpm' => $rpm,
        'fuellevel' => $fuellevel,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gv500_loc($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $vin = $params[3];
    $name = $params[4];
    $report_id = substr($params[6], 0, 1);
    $report_type = substr($params[6], 1);

    $hdop = $params[8];
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
    $speed = number_format(($params[9] * 0.53996), 1, '.', ',');
    $heading = $params[10];
    $altitude = number_format($params[11], 1, '.', ',');
    $longitude = $params[12];
    $latitude = $params[13];
    $fixTime = strtotime($params[14]) * 1000;
    $mcc = $params[15];
    $mnc = $params[16];
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
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'vin' => $vin,
        'reportid' => $report_id,
        'reporttype' => $report_type,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'network' => $network,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gv500_inf($params) {
    $imei = $params[2];
    $state = $params[5];
    if (in_array($state, array("21", "22",))) {
        $ign = '1';
    } else {
        $ign = '0';
    }
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[17]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'ign' => $ign,
        'vin' => $params[3],
        'name' => $params[4],
        'iccid' => $params[6],
        'rssi' => $params[7],
        'connected' => $params[9],
        'extv' => number_format(($params[10] * 0.001), 1),
        'battery' => $params[12],
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gv500_pfa($params) {
    $imei = $params[2];
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[5]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'vin' => $params[3],
        'name' => $params[4],
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_gv500_ign($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $vin = $params[3];
    $name = $params[4];
    $report_type = $params[6];
    $hdop = $params[8];
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
    $speed = number_format(($params[9] * 0.53996), 1, '.', ',');
    $heading = $params[10];
    $altitude = number_format($params[11], 1, '.', ',');
    $longitude = $params[12];
    $latitude = $params[13];
    $fixTime = strtotime($params[14]) * 1000;
    $mcc = $params[15];
    $mnc = $params[16];
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
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'vin' => $vin,
        'reporttype' => $report_type,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'network' => $network,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}
