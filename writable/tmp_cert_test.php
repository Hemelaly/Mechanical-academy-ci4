<?php

$root = dirname(__DIR__);
define("FCPATH", $root . "/public/");
require $root . "/vendor/autoload.php";
require $root . "/app/Config/Paths.php";

$paths = new Config\Paths();
require rtrim($paths->systemDirectory, "/\\") . "/Boot.php";

CodeIgniter\Boot::bootWeb($paths);

$svc = new App\Services\CertificateService();
$res = $svc->ensureForEnrollment(80, 113);
var_export($res);

