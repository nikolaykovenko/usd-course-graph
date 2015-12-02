<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 01.12.15
 */

namespace Application\Repository;

use Application\Entity\Currency;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

/**
 * Class CourseGraphPresenter
 * @package Application\Repository
 */
class CourseGraphPresenter extends AbstractGraphPresenter
{

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array|null
     */
    private $groups;


    /**
     * @param EntityRepository $entityRepository
     * @param int $limit
     */
    public function __construct(EntityRepository $entityRepository, $limit = 0)
    {
        $this->entityRepository = $entityRepository;
        $this->limit = $limit;

    }

    /**
     * @inheritdoc
     */
    public function getGroups()
    {
        if (is_null($this->groups)) {
            $rawData = $this->getBaseQuery()
                ->addGroupBy('currency.type')
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_OBJECT);

            $result = array_map(
                function(Currency $currency) {
                    return $currency->getType();
                },
                $rawData
            );

            $this->groups = $result;
        }

        return $this->groups;
    }

    /**
     * @inheritdoc
     */
    public function getRawData()
    {
        $query = $this->getBaseQuery()
            ->orderBy('currency.time');

        return $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * Возвращает базовый запрос на выборку
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getBaseQuery()
    {
        $result = $this->entityRepository
            ->createQueryBuilder('currency');

        if ($this->limit) {
            $result->setMaxResults($this->limit);
        }

        return $result;
    }
}
