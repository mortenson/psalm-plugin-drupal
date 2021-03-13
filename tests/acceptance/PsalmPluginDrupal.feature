Feature: Psalm Plugin Drupal
  In order to test Psalm plugins
  As a Psalm plugin developer
  I need to be able to write tests

  Background:
    Given I have the following code preamble
    """
    <?php

    """
    And I have the following config
    """
    <?xml version="1.0"?>
    <psalm
      errorLevel="6"
      resolveFromConfigFile="true"
      runTaintAnalysis="true"
      autoloader="../../../scripts/autoload.php"
    >
        <fileExtensions>
            <extension name=".php" />
            <extension name=".module" />
            <extension name=".theme" />
            <extension name=".inc" />
        </fileExtensions>
        <projectFiles>
            <directory name="../../_run"/>
        </projectFiles>
        <plugins>
            <pluginClass class="Psalm\SymfonyPsalmPlugin\Plugin">
                <containerXml>DrupalContainerDump.xml</containerXml>
            </pluginClass>
            <pluginClass class="mortenson\PsalmPluginDrupal\Plugin">
                <containerXml>DrupalContainerDump.xml</containerXml>
                <extensions>
                  <module name="node" />
                </extensions>
            </pluginClass>
        </plugins>
    </psalm>
    """

  Scenario: ContainerHandler works
    Given I have the following code
      """
      \Drupal::service('database')->query($_GET['input']);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                  | Message                                            |
      | TaintedSql            | Detected tainted SQL                               |
    And I see no other errors
    And I see exit code 2

  Scenario: Database connection SQLi
    Given I have the following code
      """
      \Drupal::database()->query($_GET['input']);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                  | Message                                            |
      | TaintedSql            | Detected tainted SQL                               |
    And I see no other errors
    And I see exit code 2

  Scenario: Database condition SQLi
    Given I have the following code
      """
      \Drupal::database()->select("node")->condition("title", "foo", $_GET['input']);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                  | Message                                            |
      | TaintedSql            | Detected tainted SQL                               |
    And I see no other errors
    And I see exit code 2

  Scenario: Markup constructor XSS
    Given I have the following code
      """
      new \Drupal\Core\StringTranslation\TranslatableMarkup($_GET['input']);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: MarkupTrait XSS
    Given I have the following code
      """
      \Drupal\Core\Render\Markup::create($_GET['input']);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  # Scenario: Render array XSS
  #   Given I have the following code
  #     """
  #     $build = [
  #       '#children' => $_GET['input'],
  #     ];
  #     """
  #   When I run Psalm in Drupal
  #   Then I see these errors
  #     | Type                   | Message                                           |
  #     | TaintedHtml            | Detected tainted HTML                             |
  #   And I see no other errors
  #   And I see exit code 2

  Scenario: Node field source
    Given I have the following code
      """
      /** @var \Drupal\node\Entity\Node $node */
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1);
      echo $node->get('title')->value;
      echo $node->title->value;
      # echo $node->getTitle();
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
      | TaintedHtml            | Detected tainted HTML                             |
      # | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: Form state source
    Given I have the following code
      """
        $form_state = new \Drupal\Core\Form\FormState();
        echo $form_state->getUserInput()['foo'];
        echo $form_state->getValue('foo');
        echo $form_state->getValues()['foo'];
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
      | TaintedHtml            | Detected tainted HTML                             |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2
