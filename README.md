# psalm-plugin-drupal

A Drupal integration for Psalm focused on security scanning (SAST) taint
analysis.

## Features

- Stubs for sinks, sources, and sanitizers
- Loading of `.module` and `.theme` files
- Autoloading of modules without an installed site
- Support for `\Drupal::service()`
- Custom script for dumping the Drupal container to XML

## Installing and running on your Drupal site

This plugin is meant to be used on your Drupal site, for the scanning of custom
modules. Note that if you follow this guide and run it on a contrib module, and
you find a valid result, you should report your findings to the Drupal Security
Team.

To install the plugin:

1. Run `composer require mortenson/psalm-plugin-drupal:*`
2. Create a `psalm.xml` file in the root of your Drupal installation like:
```xml
<?xml version="1.0"?>
<psalm
  errorLevel="6"
  resolveFromConfigFile="true"
  runTaintAnalysis="true"
  autoloader="./vendor/mortenson/psalm-plugin-drupal/scripts/autoload.php"
>
    <fileExtensions>
        <extension name=".php" />
        <extension name=".module" />
        <extension name=".theme" />
        <extension name=".inc" />
    </fileExtensions>
    <projectFiles>
        <directory name="modules/custom"/>
    </projectFiles>
    <plugins>
        <pluginClass class="Psalm\SymfonyPsalmPlugin\Plugin">
            <containerXml>DrupalContainerDump.xml</containerXml>
        </pluginClass>
        <pluginClass class="mortenson\PsalmPluginDrupal\Plugin">
            <containerXml>DrupalContainerDump.xml</containerXml>
            <extensions>
              <!-- List your modules explicitly here, as the scan may happen without a database -->
              <module name="my_custom_module" />
              <module name="my_module_dependency" />
            </extensions>
        </pluginClass>
    </plugins>
</psalm>
```
3. Run `php ./vendor/mortenson/psalm-plugin-drupal/scripts/dump_script.php && ./vendor/bin/psalm .`

## Contributing

### Running and writing tests

Tests use Codeception via [weirdan/codeception-psalm-module].

You can run tests with `composer run test`.

To write tests, edit tests/acceptance/PsalmPluginDrupal.feature and add a new
Scenario.

To run a single failing test, add the `@failing` tag above the `Scenario:` 
line, then run `composer run test-failing`.

### Checking code style

Code style should be checked before committing code.

To do this, run `composer run cs-check`, or `composer run cs-fix` to
automatically fix issues with `phpcbf`.

## License

This repo is licensed as LGPL 2.1 under the Commons Clause. This means the
source is available, but it can't be resold without my (Samuel Mortenson's)
permission. I haven't licensed things this way before, but know that Security
projects are often quickly turned into products. I want this to be free for all
Drupal users and not turned into some "premium" SAST nonsense. If you are
have questions about the license or think you have a legitimate case to resell,
contact me (my email is linked at https://mortenson.coffee).

[weirdan/codeception-psalm-module]: https://github.com/weirdan/codeception-psalm-module
