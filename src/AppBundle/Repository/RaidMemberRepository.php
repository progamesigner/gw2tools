<?php
/*
 * This file is part of the Arnapou GW2Tools package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Repository;

use AppBundle\Entity\RaidMember;
use AppBundle\Entity\RaidRoster;
use AppBundle\Entity\Token;
use Doctrine\ORM\Query\Expr\Join;
use Gw2tool\Exception\AccessNotAllowedException;

/**
 * RaidMemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RaidMemberRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param       $rosterId
     * @param Token $token
     * @throws AccessNotAllowedException
     * @return RaidMember
     */
    public function getMember($rosterId, Token $token)
    {
        $items = $this->_em->createQueryBuilder()
            ->select('m')
            ->from(RaidRoster::class, 'r')
            ->join(RaidMember::class, 'm', Join::INNER_JOIN, 'r.id = m.roster')
            ->where('m.name = :member OR r.name = :member')
            ->setParameter('member', $token->getName())
            ->andWhere('r.id = :id')
            ->setParameter('id', $rosterId)
            ->getQuery()
            ->getResult();

        if (count($items) !== 1) {
            throw new AccessNotAllowedException();
        }

        return $items[0];
    }

    /**
     * @param       $rosterId
     * @throws AccessNotAllowedException
     * @return RaidMember[]
     */
    public function getMembers($rosterId)
    {
        return $this->_em->createQueryBuilder()
            ->select('m')
            ->from(RaidRoster::class, 'r')
            ->join(RaidMember::class, 'm', Join::INNER_JOIN, 'r.id = m.roster')
            ->andWhere('r.id = :id')
            ->setParameter('id', $rosterId)
            ->orderBy('m.name')
            ->getQuery()
            ->getResult();
    }
}
