<?php

// Adicione este bloco ao arquivo app/Config/Routes.php do backend CI4.
$routes->group('api/certificates', static function ($routes) {
    $routes->post('generate', 'Api\CertificatesController::generate');
    $routes->get('verify/(:segment)', 'Api\CertificatesController::verify/$1');
    $routes->get('download/(:segment)', 'Api\CertificatesController::download/$1');
});
