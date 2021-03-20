<?php

use Drupal\Component\Serialization\Yaml;

$autoloader = require_once 'autoload.php';
require_once __DIR__ . '/PsalmDrupalKernel.php';

if (count($argv) < 2) {
    echo "Please provide a list of module paths to scan.\n";
    echo "Example: php generate_entrypoint.php modules/custom/foo,modules/custom/bar\n";
    return 1;
}

$dirs = explode(',', $argv[1]);

$entrypoint = "<?php\n";

foreach ($dirs as $dir) {
    $directory = new RecursiveDirectoryIterator($dir);
    $files = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
        if ($current->getFilename()[0] === '.') {
            return false;
        }
        if ($current->isDir()) {
            $excluded_dirs = '/(tests|node_modules|bower_components|vendor|files)/';
            return !preg_match($excluded_dirs, $current->getPathname());
        }
        return preg_match('/.*\.routing\.yml$/', $current->getPathname());
    });
    foreach (new RecursiveIteratorIterator($files) as $file) {
        $contents = file_get_contents($file->getPathname());
        $routes = Yaml::decode($contents);
        foreach ($routes as $route) {
            if (isset($route['defaults']['_controller'])) {
                $parts = explode("::", $route['defaults']['_controller']);
                if (count($parts) !== 2) {
                    continue;
                }
                $entrypoint .= '
$controller = new ' . $parts[0] . '();
$build = $controller->' . $parts[1] . '();
\Drupal::service("renderer")->render($build);';
            }
            if (isset($route['defaults']['_form'])) {
                $entrypoint .= '
$form = new ' . $route['defaults']['_form'] . '();
$form_state = new \Drupal\Core\Form\FormState();
$build = $form->buildForm([], $form_state);
\Drupal::service("renderer")->render($build);';
            }
        }
    }
}

file_put_contents('psalm_drupal_entrypoint.module', $entrypoint);
