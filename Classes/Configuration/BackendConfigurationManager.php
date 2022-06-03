<?php

namespace Socialstream\SocialStream\Configuration;


class BackendConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
{
    /**
     * @param int $currentPageId
     */
    public function setCurrentPageId(int $currentPageId): void
    {
        $this->currentPageId = $currentPageId;
    }

}
