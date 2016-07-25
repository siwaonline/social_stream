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
 * The repository for Events
 */
class EventRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $defaultOrderings = array(
        'start_time' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    public function findHiddenById($id) {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(TRUE);
        $query->matching($query->equals('id', $id));
        return $query->execute();
    }

    public function getByPages($pageIds,$count){
        $query = $this->createQuery();

        $arr = array();
        foreach($pageIds as $pageId){
            array_push($arr, $query->equals('page', $pageId));
        }
        $query->matching(
            $query->logicalOr(
                $arr
            )
        );

        if($count){
            $query->setLimit($count);
        }

        return $query->execute();
    }

    public function getPrev($time){
        $query = $this->createQuery();
        $query->setOrderings(
            array(
                'start_time' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            )
        );
        $query->matching($query->greaterThan('start_time', $time))->setLimit(1);
        return $query->execute()->getFirst();
    }
    public function getNext($time){
        $query = $this->createQuery();
        $query->matching($query->lessThan('start_time', $time))->setLimit(1);
        return $query->execute()->getFirst();
    }
}