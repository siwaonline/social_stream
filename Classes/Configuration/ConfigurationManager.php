<?php


namespace Socialstream\SocialStream\Configuration;

use TYPO3\CMS\Extbase\Configuration\AbstractConfigurationManager;

class ConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
{
    /**
     * @return AbstractConfigurationManager
     */
    public function getConcreteConfigurationManager(): AbstractConfigurationManager
    {
        return $this->concreteConfigurationManager;
    }
}
