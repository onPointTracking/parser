<?php

function build_ack($data) {
    $params = explode(',', $data);
    $tail = '+SACK:' . end($params);

    return $tail;
}

function queclink($buf) {
    # Verify Queclink device message
    if ((substr($buf, 0, 1) == '+') && (substr($buf, -1) == '$')) {
        return True;
    } else {
        return False;
    }
}

function parseQueclink($data) {
    $proto_id = substr($data[1], 0, 2);
    if (in_array($proto_id, array("F5", "D4", "C3"))) {
        return parse_gl300($data);
    }
    if ($proto_id == '56') {
        return parse_gv500($data);
    }
    if ($proto_id == '42') {
        return parse_bolt4g($data);
    }
}

function dataForwarding($buf) {
    $data = explode(",", $buf);
    if (!in_array(explode(":", $data[0])[0], array("+ACK", "+SACK"))) {
        $model = substr($data[1], 0, 2);
        if ($model == 'F5') { //GL300M
            $server_ip = '64.120.108.24';
            $server_port = 21845;
            //forward_data($server_ip, $server_port, $buf);
        } elseif ($model == 'D4') { //GL310M
            $server_ip = '64.120.108.24';
            $server_port = 22122;
            //forward_data($server_ip, $server_port, $buf);
        } elseif ($model == 'C3') { //GL320M
            $server_ip = '64.120.108.24';
            $server_port = 22180;
            //forward_data($server_ip, $server_port, $buf);
        } elseif ($model == '56') { //GV500M
            $server_ip = '64.120.108.24';
            $server_port = 22107;
            //forward_data($server_ip, $server_port, $buf);
        } elseif ($model == '42') { //Bolt4G
            $server_ip = '64.120.108.24';
            $server_port = 21874;
            //forward_data($server_ip, $server_port, $buf);
        }
    }
    if (isset($server_ip)) {
        $f = socket_create(AF_INET, SOCK_DGRAM, 0);
        socket_sendto($f, $buf, 512, 0, $server_ip, $server_port);
    } else {
        return NULL;
    }
}
