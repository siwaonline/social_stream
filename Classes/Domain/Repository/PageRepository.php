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
 * The repository for Pages
 */
class PageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function searchByName($name,$streamtype){
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('name', $name),
                $query->equals('streamtype', $streamtype)
            )
        );
        return $query->execute();
    }
    public function searchById($id,$streamtype){
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('id', $id),
                $query->equals('streamtype', $streamtype)
            )
        );
        return $query->execute()->getFirst();
    }
    
}