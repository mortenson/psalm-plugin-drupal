<?php

$autoloader = require_once 'autoload.php';
require_once __DIR__ . '/PsalmDrupalKernel.php';

use Drupal\Component\FileCache\FileCacheFactory;
use Symfony\Component\HttpFoundation\Request;

FileCacheFactory::setConfiguration(['default' => ['class' => '\Drupal\Component\FileCache\NullFileCache']]);
$request = Request::createFromGlobals();
$kernel = PsalmDrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->compilePsalmContainer();
