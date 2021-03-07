<?php

namespace mortenson\PslamPluginDrupal;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use mortenson\PslamPluginDrupal\Hooks;
use mortenson\PslamPluginDrupal\ContainerHandler;
use Psalm\Exception\ConfigException;
use Psalm\SymfonyPsalmPlugin\Symfony\ContainerMeta;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/Hooks.php';
        require_once __DIR__ . '/ContainerHandler.php';
        $psalm->registerHooksFromClass(Hooks::class);

        foreach ($this->getStubFiles() as $file) {
            $psalm->addStubFile($file);
        }

        if (!$config) {
            return;
        }

        if (isset($config->containerXml)) {
            ContainerHandler::init(new ContainerMeta((array) $config->containerXml));
        }

        $psalm->registerHooksFromClass(ContainerHandler::class);

        // @todo Better way to do this? Works fine for now.
        $xmlPath = realpath($config->containerXml);
        if (!$xmlPath || !file_exists($xmlPath)) {
            return;
        }

        $xml = simplexml_load_file($xmlPath);
        if (!$xml->parameters instanceof \SimpleXMLElement) {
            throw new ConfigException($xmlPath . ' is not a valid Drupal container xml file');
        }
        /** @var \SimpleXMLElement $parameter */
        foreach ($xml->parameters->children() as $parameter) {
            $attributes = $parameter->attributes();
            if ($attributes->key == 'container.modules') {
                /** @var \SimpleXMLElement $parameter */
                foreach ($parameter as $module) {
                    /** @var \SimpleXMLElement $child */
                    $filename = '';
                    $pathname = '';
                    foreach ($module->children() as $child) {
                        $childAttributes = $child->attributes();
                        if ($childAttributes->key == 'filename') {
                            $filename = (string) $child;
                        } elseif ($childAttributes->key == 'pathname') {
                            $pathname = (string) $child;
                        }
                    }
                    if (!$filename || !$pathname) {
                        continue;
                    }
                    $filePath = dirname($pathname) . '/' . $filename;
                    if (file_exists($filePath)) {
                        $psalm->addStubFile($filePath);
                    }
                }
            }
        }

        // Add all themes for now. Really messy part of core.
        $directory = new RecursiveDirectoryIterator('.');
        $files = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if ($current->isDir()) {
                $excluded_dirs = '/(node_modules|bower_components|vendor|files)/';
                return !preg_match($excluded_dirs, $current->getPathname());
            }
            return preg_match('/themes\/.*(\.php|\.theme)$/', $current->getPathname());
        });
        foreach (new RecursiveIteratorIterator($files) as $file) {
            $psalm->addStubFile($file->getPathname());
        }
    }

    /** @return list<string> */
    private function getStubFiles(): array
    {
        return glob(__DIR__ . '/stubs/*.php') ?: [];
    }
}
