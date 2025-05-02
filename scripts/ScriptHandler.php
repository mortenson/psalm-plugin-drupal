<?php

declare(strict_types=1);

use Drupal\Core\Site\Settings;
use Drupal\Core\Site\SettingsEditor;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Composer scripts.
 */
class ScriptHandler
{
  /**
   * Creates required files for Drupal and tests to work.
   */
    public static function createRequiredFiles(): void
    {
        $fs = new Filesystem();
        $drupalRoot = __DIR__ . '/../tests/_tmp/drupal';

        // Prepare the settings file for installation.
        if (
            !$fs->exists($drupalRoot . '/sites/default/settings.php')
            && $fs->exists($drupalRoot . '/sites/default/default.settings.php')
        ) {
            $fs->copy($drupalRoot . '/sites/default/default.settings.php', $drupalRoot . '/sites/default/settings.php');
            require_once $drupalRoot . '/core/includes/bootstrap.inc';
            require_once $drupalRoot . '/core/includes/install.inc';
            new Settings([]);
            $settings['settings']['config_sync_directory'] = (object) [
            'value' => '../config/sync',
            'required' => true,
            ];
            $settings['databases']['default']['default'] = (object) [
            'value' => [
            'driver' => 'sqlite',
            'database' => $drupalRoot . '/db.sqlite',
            ],
            'required' => true,
            ];

            SettingsEditor::rewrite($drupalRoot . '/sites/default/settings.php', $settings);
            $fs->chmod($drupalRoot . '/sites/default/settings.php', 0666);
        }
    }
}
