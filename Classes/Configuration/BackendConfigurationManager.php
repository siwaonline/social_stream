<?php

namespace Socialstream\SocialStream\Configuration;


class BackendConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
{
    /**
     * @return int
     */
    public function getCurrentPageId(): int
    {
        return $this->currentPageId;
    }

    /**
     * @param int $currentPageId
     */
    public function setCurrentPageId(int $currentPageId): void
    {
        $this->currentPageId = $currentPageId;
    }

}