<?php

use Drupal\Core\DrupalKernel;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;

class PsalmDrupalKernel extends DrupalKernel
{
    private function getPsalmModuleList()
    {
        if (!file_exists('psalm.xml')) {
            return [];
        }
        $xml = simplexml_load_file('psalm.xml');
        if (!$xml) {
            return [];
        }
        $modules = [];
        foreach ($xml->xpath('//pluginClass[@class="mortenson\PsalmPluginDrupal\Plugin"]/extensions/module') as $node) {
            $attributes = $node->attributes();
            if (!empty($attributes['name'])) {
                $modules[] = (string) $attributes['name'];
            }
        }
        return $modules;
    }

    public function compilePsalmContainer($extra_modules = [])
    {
        foreach ($this->getPsalmModuleList() as $module) {
            $this->moduleList[$module] = $module;
        }
        foreach ($extra_modules as $module) {
            $this->moduleList[$module] = $module;
        }
        return $this->compileContainer();
    }

    public function dumpContainerXml($extra_modules = [])
    {
        $container = $this->compilePsalmContainer($extra_modules);
        $dumper = new XmlDumper($container);
        $dump = $dumper->dump();
        file_put_contents('./DrupalContainerDump.xml', $dump);
    }
}
