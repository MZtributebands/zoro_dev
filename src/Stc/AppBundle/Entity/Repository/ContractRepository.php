<?php

namespace Stc\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Symfony\Component\Config\Definition\Exception\Exception;

class ContractRepository extends EntityRepository
{
    /**
     * @return integer
     */
    public function getContractCountByStage()
    {

    }

    public function getAssociatedPerformance($contract_id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(array('pc'))
            ->from('stc_performance_contracts', 'pc')
            ->where('pc.contract_id = ?1')
            ->setParameter(1, $contract_id);

        //convert query builder instance into a Query object to execute it:
        $query = $qb->getQuery();
        $result = $query->getArrayResult();

        return $result;
    }

}