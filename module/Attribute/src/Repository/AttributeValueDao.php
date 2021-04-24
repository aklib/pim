<?php
    /**
     * Class AttributeValueDao
     * @package Attribute\Repository
     *
     * since: 12.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Repository;


    use Doctrine\ORM\NonUniqueResultException;
    use Doctrine\ORM\NoResultException;

    class AttributeValueDao extends AbstractAttributeDao
    {
        public function countAttributeValues(int $attributeId): int
        {
            if(empty($attributeId)){
                return 0;
            }
            $qb = $this->createQueryBuilder('v');
            $qb
                ->select('COUNT(v.id)')
                ->where('v.attributeId = :attributeId')->setParameter('attributeId',$attributeId);
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NoResultException $e) {
            } catch (NonUniqueResultException $e) {
            }
            return 0;
        }
    }