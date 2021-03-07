<?php

namespace mortenson\PslamPluginDrupal;

use Drupal\Core\DrupalKernel;
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
