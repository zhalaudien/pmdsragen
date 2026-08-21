<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Pendataan::index');
$routes->get('pendataan', 'Pendataan::index');
$routes->post('pendataan/simpan', 'Pendataan::simpan');
$routes->get('pendataan/sukses', 'Pendataan::sukses');
