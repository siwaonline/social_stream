<?php

namespace Socialstream\SocialStream\Hooks;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Socialstream\SocialStream\Domain\Repository\ChannelRepository;
use Socialstream\SocialStream\Utility\Feed\FeedUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * ChannelProcessDatamap
 */
class ChannelProcessDatamap
{
    private $channelRepository;

    /**
     * Inject the product repository
     *
     * @param ChannelRepository $channelRepository
     */
    public function injectProductRepository(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }


    function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$reference)
    {
        if (array_key_exists('hidden', $fieldArray)) {
            return;
        } else {
            if ($table == 'tx_socialstream_domain_model_channel') {
                if ($status == "update") {
                    $channel = $this->channelRepository->findHidden($id)->getFirst();
                } else {
                    $channel = new \Socialstream\SocialStream\Domain\Model\Channel();
                }
                if (array_key_exists('type', $fieldArray)) $channel->setType($fieldArray["type"]);
                if (array_key_exists('object_id', $fieldArray)) $channel->setObjectId($fieldArray["object_id"]);
                if (array_key_exists('token', $fieldArray)) $channel->setToken($fieldArray["token"]);
                if ($channel) {
                    if (($channel->getObjectId() && $channel->getToken()) || ($channel->getType() === "youtube" && $channel->getToken()) || ($channel->getType() === "youtube" && $channel->getObjectId())) {

                        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
                        $pageRes = $queryBuilder->select("pid")->from("pages")->where($queryBuilder->expr()->eq('uid', $reference->checkValue_currentRecord["pid"]))->setMaxResults(1)->execute()->fetchAll();
                        foreach ($pageRes as $page) {
                            $pid = $page['pid'];
                        }
                        if ($pid <= 0) $pid = $reference->checkValue_currentRecord["pid"];

                        $utility = FeedUtility::getUtility($channel->getType(), $pid);
                        $channel = $utility->getChannel($channel, 1);

                        if ($channel) {
                            // $fieldArray['object_id'] = $channel->getObjectId();
                            $fieldArray['title'] = $channel->getTitle();
                            $fieldArray['about'] = $channel->getAbout();
                            //$fieldArray['description'] = $channel->getDescription();
                            $fieldArray['link'] = $channel->getLink();
                        } else {
                            $fieldArray = array();
                        }
                    }
                }
            }
        }
    }

}
