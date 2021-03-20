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
            <file name="psalm_drupal_entrypoint.module"></file>
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

  Scenario: Render array bad keys
    Given I have the following code
      """
      $build = [
        '#markup' => $_GET['input'],
        '#template' => $_GET['input'],
      ];
      \Drupal::service('renderer')->render($build);
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: Render array safe keys
    Given I have the following code
      """
      $build = [
        '#markup' => $_GET['input'],
      ];
      \Drupal::service('renderer')->render($build);
      """
    When I run Psalm in Drupal
    Then I see no other errors
    And I see exit code 0

  Scenario: Render array from controller
    Given I have the following code in "my_module.routing.yml"
      """
      my_module.page:
        path: '/my-module'
        defaults:
          _controller: '\Drupal\my_module\Controller\MyController::build'
      my_module.page_response:
        path: '/my-module-response'
        defaults:
          _controller: '\Drupal\my_module\Controller\MyController::buildResponse'
      """
    Given I have the following code
      """
      namespace Drupal\my_module\Controller;

      use Symfony\Component\HttpFoundation\Response;
      use Drupal\Core\Controller\ControllerBase;

      class MyController extends ControllerBase {

        /**
         * @return array
         */
        public function build() {
          return [
            '#markup' => $_GET['input'],
            '#template' => $_GET['input'],
          ];
        }

        /**
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function buildResponse() {
          return new Response('');
        }

      }
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: Render array from form
    Given I have the following code in "my_module.routing.yml"
      """
      my_module.form:
        path: '/my-module-form'
        defaults:
          _form: '\Drupal\my_module\Form\MyForm'
      """
    Given I have the following code
      """
      namespace Drupal\my_module\Form;

      use Symfony\Component\HttpFoundation\Response;
      use Drupal\Core\Form\FormBase;

      class MyForm extends FormBase {

        /**
         * @return array
         */
        public function buildForm() {
          return [
            '#markup' => $_GET['input'],
            '#template' => $_GET['input'],
          ];
        }

      }
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
    And I see no other errors
    And I see exit code 2

  Scenario: Node field source
    Given I have the following code
      """
      /** @var \Drupal\node\Entity\Node $node */
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1);
      echo $node->get('title')->value;
      echo $node->title->value;
      echo $node->getTitle();
      """
    When I run Psalm in Drupal
    Then I see these errors
      | Type                   | Message                                           |
      | TaintedHtml            | Detected tainted HTML                             |
      | TaintedHtml            | Detected tainted HTML                             |
      | TaintedHtml            | Detected tainted HTML                             |
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
