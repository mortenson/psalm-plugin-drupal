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
        $this->writeToFile('tests/_tmp/drupal/composer.lock', '{}');
    }

    /**
     * @When I run Psalm in Drupal
     */
    public function iRunPsalmInDrupal(): void
    {
        $this->runPsalmIn('tests/_tmp/drupal');
    }
}
