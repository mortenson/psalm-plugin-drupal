![Test Status](https://github.com/mortenson/psalm-plugin-drupal/actions/workflows/tests.yml/badge.svg)

# psalm-plugin-drupal

A Drupal integration for Psalm focused on security scanning (SAST) taint
analysis.

## Features

- Stubs for sinks, sources, and sanitizers
- Loading of `.module` and `.theme` files
- Autoloading of modules without an installed site
- Support for `\Drupal::service()`
- Custom script for dumping the Drupal container to XML
- Support for detecting tainted render arrays
- Novel support for Controllers and Form methods.

## Installing and running on your Drupal site

This plugin is meant to be used on your Drupal site, for the scanning of custom
modules. Note that if you follow this guide and run it on a contrib module, and
you find a valid result, you should report your findings to the Drupal Security
Team.

To install the plugin:

1. Run `composer require mortenson/psalm-plugin-drupal:dev-master --dev`
2. Change directories to the root of your Drupal installation (ex: `cd web`, `cd docroot`).
3. Create a `psalm.xml` file in the root of your Drupal installation like:
```xml
<?xml version="1.0"?>
<psalm
  errorLevel="6"
  resolveFromConfigFile="true"
  runTaintAnalysis="true"
  autoloader="../vendor/mortenson/psalm-plugin-drupal/scripts/autoload.php"
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
4. Run `php ../vendor/mortenson/psalm-plugin-drupal/scripts/dump_script.php && ../vendor/bin/psalm .`

Note that the path to `vendor` may change based on your Drupal installation.

### Generating an entrypoint for seemingly unused class methods

Drupal's code paths aren't always clear, especially in Drupal 8. Because of
this, things like Controller methods (aka route callbacks) will not be
analyzed when running Psalm.

To have Psalm analyze these paths, you'll need to generate an entrypoint file
that executes the methods you want to test.

A script has been included for you to generate this entrypoint for you. To use
it, do the following:

1. Run `php ../vendor/mortenson/psalm-plugin-drupal/scripts/generate_entrypoint.php <comma separated paths to your custom modules>`
2. Add `<file name="psalm_drupal_entrypoint.module"></file>` to your
`psalm.xml` file, under the `<projectFiles>` node.
3. Run Psalm.

Currently, only `routing.yml` files are parsed to generate the entrypoint,
focusing on Controller and Form methods.

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

[weirdan/codeception-psalm-module]: https://github.com/weirdan/codeception-psalm-module
