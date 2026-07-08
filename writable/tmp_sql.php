<?php
$db = new mysqli("localhost","root","","academy",3306);
$res = $db->query("SHOW COLUMNS FROM certificates LIKE 'status_certificate'");
var_export($res->fetch_assoc());
