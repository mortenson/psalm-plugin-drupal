Feature: Psalm Plugin Drupal
  In order to test Psalm plugins
  As a Psalm plugin developer
  I need to be able to write tests

  Background:
    Given I have the following code preamble
    """
    <?php

    """
    And I have empty composer.lock
    And I have the following config
    """
    <?xml version="1.0"?>
    <psalm
      errorLevel="6"
      resolveFromConfigFile="true"
      runTaintAnalysis="true"
    >
        <fileExtensions>
            <extension name=".php" />
            <extension name=".module" />
            <extension name=".theme" />
        </fileExtensions>
        <projectFiles>
            <directory name="."/>
        </projectFiles>
        <plugins>
            <pluginClass class="mortenson\PslamPluginDrupal\Plugin">
                <containerXml>DrupalContainerDump.xml</containerXml>
            </pluginClass>
        </plugins>
    </psalm>
    """
    And I have Drupal installed

  Scenario: .module files loaded
    Given I have the following code
      """
      node_echo($_GET['input']);
      """
    When I run Psalm
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: .theme files loaded
    Given I have the following code
      """
      bartik_echo($_GET['input']);
      """
    When I run Psalm
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: Database connection SQLi
    Given I have the following code
      """
      class Select extends \Drupal\Core\Database\Connection {};
      $connection = new Select();
      $connection->query($_GET['input']);
      """
    When I run Psalm
    Then I see these errors
      | Type                  | Message                                            |
      | TaintedSql            | Detected tainted SQL                               |
    And I see no other errors
    And I see exit code 2

  Scenario: Translatable XSS
    Given I have the following code
      """
      $foo = new \Drupal\Core\StringTranslation\TranslatableMarkup($_GET['input']);
      """
    When I run Psalm
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2
