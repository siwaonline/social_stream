<?php


namespace Socialstream\SocialStream\Configuration;

class ConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
{
    /**
     * @return AbstractConfigurationManager
     */
    public function getConcreteConfigurationManager(): ConfigurationManager
    {
        return $this->concreteConfigurationManager;
    }
}
