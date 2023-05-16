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
    file_get_contents('https://d1.recoverylocate.com/insert.php' . $uri);
}

function parse_bolt4g_fri($params) {
    $type = substr(explode(':', $params[0])[1], 2);
    $imei = $params[2];
    $name = $params[3];
    $report_id = $params[4];
    $report_type = $params[5];
    $motion = $params[6];
    $wms_mode = $params[7];
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
    $wms_mode = $params[6];
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
    $rssi = $params[19];
    $batterylevel = $params[21];
    $attributes = array(
        'type' => $type,
        'name' => $name,
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
    $wms_mode = $params[4];
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
    $batterylevel = $params[22];
    $attributes = array(
        'type' => $type,
        'name' => $name,
        'reporttype' => $report_type,
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
    include 'inc/db_inc.php';
    $imei = $params[2];

    $stmt = $mysqli->prepare('SELECT * FROM gpsdata_traccar.devices WHERE uniqueId = ?');
    $stmt->bind_param('s', $imei);
    $stmt->execute();
    $result = $stmt->get_result();
    $device = $result->fetch_assoc();
    $stmt->close();

    $speed = $device['speed'];
    $heading = $device['course'];
    $altitude = $device['altitude'];
    $longitude = $device['lastValidLongitude'];
    $latitude = $device['lastValidLatitude'];

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
    if ($device) {
        $uri = '?uniqueId=' . $imei . '&altitude=' . $altitude . '&protocol=gl200&course=' . $heading . '&longitude=' . $longitude . '&latitude=' . $latitude . '&speed=' . $speed . '&fixTime=' . $fixTime . '&attributes=' . json_encode($attributes);
    } else {
        $uri = NULL;
    }
    return $uri;
}

function parse_bolt4g_pfa($params) {
    return NULL;
}
