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
 * The repository for Posts
 */
class PostRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $defaultOrderings = array(
        'created_time' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    public function findHiddenById($id,$page) {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(TRUE);
        $query->matching(
            $query->equals('id', $id),
            $query->equals('page', $page)
        );
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

    public function getPrev($page,$time){
        $query = $this->createQuery();
        $query->setOrderings(
            array(
                'created_time' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            )
        );
        $query->matching(
            $query->logicalAnd(
                $query->equals('page', $page),
                $query->greaterThan('created_time', $time)
            )
        );
        $query->setLimit(1);
        return $query->execute()->getFirst();
    }
    public function getNext($page,$time){
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('page', $page),
                $query->lessThan('created_time', $time)
            )
        );
        return $query->execute()->getFirst();
    }
}