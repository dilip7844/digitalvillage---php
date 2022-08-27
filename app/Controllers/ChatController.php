<?php
//$host="app.pachal.in";
$host="localhost";
$port=8085;
$socket=socket_create(AF_INET,SOCK_STREAM,0) or die('Not Created');
$result=socket_bind($socket,$host,$port) or die('Not bind');
$result=socket_listen($socket,3) or die('Not listen');
//echo $result;

do{
$accept=socket_accept($socket) or die('Not accept');
$msg=socket_read($accept,1024);
$msg=trim($msg);
echo "\nClient Reply : ".$msg."\n\n";

//echo "Enter reply";
//$reply=fgets(STDIN);
socket_write($accept,$msg,strlen($msg));
}while(true);

socket_close($socket);
?>