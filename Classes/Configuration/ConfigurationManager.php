<?php


namespace Socialstream\SocialStream\Configuration;


use TYPO3\CMS\Extbase\Configuration\AbstractConfigurationManager;

class ConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
{
    protected function initializeConcreteConfigurationManager()
    {
        if ($this->environmentService->isEnvironmentInFrontendMode()) {
            $this->concreteConfigurationManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager::class);
        } else {
            $this->concreteConfigurationManager = $this->objectManager->get(BackendConfigurationManager::class);
        }
    }

    /**
     * @return AbstractConfigurationManager
     */
    public function getConcreteConfigurationManager(): AbstractConfigurationManager
    {
        return $this->concreteConfigurationManager;
    }



}