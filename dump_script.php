<?php

use Drupal\Component\FileCache\FileCacheFactory;
use Symfony\Component\HttpFoundation\Request;
use mortenson\PslamPluginDrupal\DumpKernel;

$autoloader = require_once 'autoload.php';
require __DIR__ . '/DumpKernel.php';

FileCacheFactory::setConfiguration(['default' => ['class' => '\Drupal\Component\FileCache\NullFileCache']]);
$request = Request::createFromGlobals();
$kernel = DumpKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->dumpContainerXml();
