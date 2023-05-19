<?php

function parse_bolt4g($data) {
    if (in_array(explode(":", $data[0])[0], array("+RESP", "+BUFF"))) {
        if (explode(":", $data[0])[1] == "GTFRI") {
            $uri = parse_bolt4g_fri($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTPNL", "GTNMR", "GTDOG"))) {
            $uri = parse_bolt4g_nmr($data);
        } elseif (explode(":", $data[0])[1] == "GTINF") {
            $uri = parse_bolt4g_inf($data);
        } elseif (in_array(explode(":", $data[0])[1], array("GTPFA", "GTPNA"))) {
            $uri = parse_bolt4g_pfa($data);
        } elseif (explode(":", $data[0])[1] == "GTDSW") {
            $uri = parse_bolt4g_dsw($data);
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

function parse_bolt4g_fri($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $name = $params[3];
    $report_id = $params[4];
    $report_type = $params[5];
    $motion = $params[6];
    switch ($params[7]) {
        case '0':
            $wms_mode = 'Normal';
            break;
        case '1':
            $wms_mode = 'Eco';
            break;
        case '2':
            $wms_mode = 'Park';
            break;
        case '3':
            $wms_mode = 'Pursuit';
            break;
        case '4':
            $wms_mode = 'Flight';
            break;
        case '5':
            $wms_mode = 'Logging';
            break;
        default:
            $wms_mode = $params[7];
            break;
    }
    $hdop = $params[9];
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
    $speed = number_format(($params[10] * 0.53996), 1, '.', ',');
    $heading = $params[11];
    $altitude = number_format($params[12], 1, '.', ',');
    $longitude = $params[13];
    $latitude = $params[14];
    $fixTime = strtotime($params[15]) * 1000;
    $mcc = $params[16];
    $mnc = $params[17];
    switch (true) {
        case strstr($mnc, '260'):
            $network = 'T-Mobile';
            break;
        case strstr($mnc, '410'):
            $network = 'ATT';
            break;
        case strstr($mnc, '480');
            $network = 'Verizon';
            break;
        default:
            $network = $mnc;
    }
    $rssi = $params[20];
    $batterylevel = $params[22];
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'reportid' => $report_id,
        'reporttype' => $report_type,
        'move' => $motion,
        'working_mode' => $wms_mode,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'rssi' => $rssi,
        'network' => $network,
        'batterylevel' => $batterylevel,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_bolt4g_nmr($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $name = $params[3];
    $report_id = $params[4];
    $report_type = $params[5];
    if ($type == 'NMR') {
        $type = 'NMR' . $report_type;
    }

    switch ($params[6]) {
        case '0':
            $wms_mode = 'Normal';
            break;
        case '1':
            $wms_mode = 'Eco';
            break;
        case '2':
            $wms_mode = 'Park';
            break;
        case '3':
            $wms_mode = 'Pursuit';
            break;
        case '4':
            $wms_mode = 'Flight';
            break;
        case '5':
            $wms_mode = 'Logging';
            break;
        default:
            $wms_mode = $params[7];
            break;
    }
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
        case strstr($mnc, '260'):
            $network = 'T-Mobile';
            break;
        case strstr($mnc, '410'):
            $network = 'ATT';
            break;
        case strstr($mnc, '480');
            $network = 'Verizon';
            break;
        default:
            $network = $mnc;
    }
    $rssi = $params[19];
    $batterylevel = $params[21];
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'move' => $report_id,
        'reportid' => $report_id,
        'reporttype' => $report_type,
        'working_mode' => $wms_mode,
        'hdop' => $hdop,
        'gpslevel' => $gps_level,
        'mcc' => $mcc,
        'mnc' => $mnc,
        'rssi' => $rssi,
        'network' => $network,
        'batterylevel' => $batterylevel,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_bolt4g_dsw($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $name = $params[3];
    switch ($params[4]) {
        case '0':
            $wms_mode = 'Normal';
            break;
        case '1':
            $wms_mode = 'Eco';
            break;
        case '2':
            $wms_mode = 'Park';
            break;
        case '3':
            $wms_mode = 'Pursuit';
            break;
        case '4':
            $wms_mode = 'Flight';
            break;
        case '5':
            $wms_mode = 'Logging';
            break;
        default:
            $wms_mode = $params[7];
            break;
    }
    $report_type = $params[5];
    $hdop = $params[10];
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
    $speed = number_format(($params[11] * 0.53996), 1, '.', ',');
    $heading = $params[12];
    $altitude = number_format($params[13], 1, '.', ',');
    $longitude = $params[14];
    $latitude = $params[15];
    $fixTime = strtotime($params[16]) * 1000;
    $mcc = $params[17];
    $mnc = $params[18];
    switch (true) {
        case strstr($mnc, '260'):
            $network = 'T-Mobile';
            break;
        case strstr($mnc, '410'):
            $network = 'ATT';
            break;
        case strstr($mnc, '480');
            $network = 'Verizon';
            break;
        default:
            $network = $mnc;
    }
    $batterylevel = $params[22];
    $attributes = array(
        'type' => $type . $report_type,
        'name' => $name,
        'reporttype' => $report_type,
        'move' => $report_type,
        'working_mode' => $wms_mode,
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

function parse_bolt4g_inf($params) {
    $imei = $params[2];
    $name = $params[3];
    switch ($params[4]) {
        case '0':
            $wms_mode = 'Normal';
            break;
        case '1':
            $wms_mode = 'Eco';
            break;
        case '2':
            $wms_mode = 'Park';
            break;
        case '3':
            $wms_mode = 'Pursuit';
            break;
        case '4':
            $wms_mode = 'Flight';
            break;
        case '5':
            $wms_mode = 'Logging';
            break;
        default:
            $wms_mode = $params[7];
            break;
    }
    $state = $params[5];
    if ($state = '42') {
        $move = '1';
    } else {
        $move = '0';
    }
    $iccid = $params[6];
    $rssi = $params[7];
    $batv = $params[12];
    $batp = $params[19];
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[18]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'name' => $name,
        'reporttype' => $state,
        'move' => $move,
        'iccid' => $iccid,
        'rssi' => $rssi,
        'battery' => $batv,
        'batterylevel' => $batp,
        'working_mode' => $wms_mode,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}

function parse_bolt4g_pfa($params) {
    $imei = $params[2];
    switch ($params[4]) {
        case '0':
            $wms_mode = 'Normal';
            break;
        case '1':
            $wms_mode = 'Eco';
            break;
        case '2':
            $wms_mode = 'Park';
            break;
        case '3':
            $wms_mode = 'Pursuit';
            break;
        case '4':
            $wms_mode = 'Flight';
            break;
        case '5':
            $wms_mode = 'Logging';
            break;
        default:
            $wms_mode = $params[7];
            break;
    }
    $speed = 0.0;
    $heading = 0;
    $altitude = 0.0;
    $longitude = 0.000000;
    $latitude = 0.000000;
    $fixTime = strtotime($params[6]) * 1000;
    $attributes = array(
        'type' => substr(explode(':', $params[0])[1], 2),
        'name' => $params[3],
        'working_mode' => $wms_mode,
        'tail' => substr(end($params), 0, 4)
    );
    $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&valid=false&attributes=' . json_encode($attributes);

    return $uri;
}
