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

use Socialstream\SocialStream\Utility\Feed\FeedUtility;

/**
 * ChannelProcessDatamap
 */
class ChannelProcessDatamap
{
    
    function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$reference) {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $channelRepository = $objectManager->get('Socialstream\\SocialStream\\Domain\\Repository\\ChannelRepository');

        if ($table == 'tx_socialstream_domain_model_channel') {
            if($status == "update") {
                $channel = $channelRepository->findHidden($id)->getFirst();
            }else {
                $channel = new \Socialstream\SocialStream\Domain\Model\Channel();
            }
            if(array_key_exists('type', $fieldArray))$channel->setType($fieldArray["type"]);
            if(array_key_exists('object_id', $fieldArray))$channel->setObjectId($fieldArray["object_id"]);
            if(array_key_exists('token', $fieldArray))$channel->setToken($fieldArray["token"]);
            if($channel){
                if($channel->getObjectId() && $channel->getToken()) {

                    $utility = FeedUtility::getUtility($channel->getType());
                    $channel = $utility->getChannel($channel,1);

                    if($channel) {
                        $fieldArray['object_id'] = $channel->getObjectId();
                        $fieldArray['title'] = $channel->getTitle();
                        $fieldArray['about'] = $channel->getAbout();
                        //$fieldArray['description'] = $channel->getDescription();
                        $fieldArray['link'] = $channel->getLink();
                    }
                    else{
                        $fieldArray = array();
                    }
                }
            }
        }
    }
    
}