<?php

// Create a UDP socket
if (!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
file_put_contents($log_file, "Socket created \n", FILE_APPEND);

// Bind Socket to Port 6004
if (!socket_bind($sock, "0.0.0.0", 6004)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not bind socket : [$errorcode] $errormsg \n");
}
file_put_contents($log_file, "Waiting for data ... \n", FILE_APPEND);
