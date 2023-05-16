<?php
require_once 'inc/_variables.php';
require_once 'inc/_functions.php';
require_once 'inc/_parsers.php';
require_once 'inc/_sockets.php';

while (1) {
    // Receive tracker data
    $r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
    $client = str_pad($remote_ip . ' : ' . $remote_port, 23);
    $data = explode(",", $buf);
    echo "$client => " . $buf . "\n";
    file_put_contents($log_file, date('Y-m-d_h:i:s', time()) . " - $client => " . $buf . "\n", FILE_APPEND);
    // Check message matches @Track protocol
    if (queclink($buf)) {
        $ack = build_ack($buf);
        socket_sendto($sock, $ack, 11, 0, $remote_ip, $remote_port);
        echo "$client <= " . $ack . "\n";
        file_put_contents($log_file, date('Y-m-d_h:i:s', time()) . " - $client <= " . $ack . "\n", FILE_APPEND);
        parseQueclink($data);
        // Temporary Data Forwarding - Delete in production
        dataForwarding($buf);
    }
}
socket_close($sock);
