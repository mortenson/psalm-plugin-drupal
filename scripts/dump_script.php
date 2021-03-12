<?php

$autoloader = require_once 'autoload.php';

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;

class DumpKernel extends DrupalKernel
{

    public function dumpContainerXml()
    {
        $container = $this->compileContainer();
        $dumper = new XmlDumper($container);
        $dump = $dumper->dump();
        file_put_contents('./DrupalContainerDump.xml', $dump);
    }

}

FileCacheFactory::setConfiguration(['default' => ['class' => '\Drupal\Component\FileCache\NullFileCache']]);
$request = Request::createFromGlobals();
$kernel = DumpKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->dumpContainerXml();
