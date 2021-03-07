<?php

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @Given I have empty composer.lock
     */
    public function iHaveEmptyComposerlock(): void
    {
        $this->writeToFile('tests/_run/composer.lock', '{}');
    }

    /**
     * @Given I have Drupal installed
     */
    public function iHaveDrupalInstalled(): void
    {
        $this->copyDir('tests/_drupal/modules', 'tests/_run/modules');
        $this->copyDir('tests/_drupal/themes', 'tests/_run/themes');
        $this->writeToFile('tests/_run/DrupalContainerDump.xml', file_get_contents('tests/_drupal/DrupalContainerDump.xml'));
    }
}
