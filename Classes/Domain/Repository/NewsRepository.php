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

/**
 * The repository for News
 */
class NewsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findHiddenById($objectId, $channel, $split = 0)
    {
        if ($split) {
            $id = explode("_", $objectId);
            $id = array_values(array_slice($id, -1))[0];
        } else {
            $id = $objectId;
        }
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(TRUE);
        $query->matching(
            $query->logicalAnd(
                $query->equals('object_id', $id),
                $query->equals('channel', $channel)
            )
        );
        return $query->execute()->getFirst();
    }

    public function findAllRawByCurrentYearFolder($channel)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->like('link', '%' . date('Y') . '/%'),
                $query->equals('channel', $channel)
            )
        );

        return $query->execute(true);
    }

    public function findAllRawByArchivFolder($channel)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->like('link', '%' . 'Archiv' . '/%'),
                $query->equals('channel', $channel)
            )
        );

        return $query->execute(true);
    }

    public function findAllRawByLastYearFolder($channel)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->like('link', '%' . (intval(date("Y")) - 1) . '/%'),
                $query->equals('channel', $channel)
            )
        );

        return $query->execute(true);
    }
}
