<?php

namespace mortenson\PslamPluginDrupal;

use SimpleXMLElement;
use Psalm\Plugin\RegistrationInterface;
use mortenson\PslamPluginDrupal\Hooks;
use mortenson\PslamPluginDrupal\ContainerHandler;
use Psalm\Plugin\PluginEntryPointInterface;
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

        // Add all themes for now. Really messy part of core.
        $directory = new RecursiveDirectoryIterator('.');
        $files = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if ($current->isDir()) {
                $excluded_dirs = '/(tests|node_modules|bower_components|vendor|files)/';
                return !preg_match($excluded_dirs, $current->getPathname());
            }
            return preg_match('/(themes|modules)\/.*(\.module|\.theme)$/', $current->getPathname());
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
