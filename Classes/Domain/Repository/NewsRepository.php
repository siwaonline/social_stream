<?php

namespace Socialstream\SocialStream\Domain\Repository;


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

use Socialstream\SocialStream\Domain\Model\Channel;
use Socialstream\SocialStream\Domain\Model\News;

/**
 * The repository for News
 */
class NewsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @param int $objectId
     * @param Channel $channel
     * @return News
     */
    public function findHiddenById($objectId, $channel)
    {
        $id = explode("_", $objectId);
        $id = array_values(array_slice($id, -1))[0];
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching(
            $query->equals('object_id', $id),
            $query->equals('channel', $channel)
        );

        return $query->execute()->getFirst();
    }

}