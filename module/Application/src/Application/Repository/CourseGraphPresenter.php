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
class CourseGraphPresenter
{

    /** @var EntityRepository */
    private $entityRepository;

    /**
     * @var int
     */
    private $limit;


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
     * Возвращает элементы в json формате необходимом для JS библиотеки
     * @return string
     */
    public function getItems()
    {
        $rawData = $this->getRawData();
        $formattedData = $this->graphFormat($rawData);

        return json_encode($formattedData);
    }

    /**
     * Возвращает группы в json формате необходимом для JS библиотеки
     * @return string
     */
    public function getGroups()
    {
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

        return json_encode($result);
    }



    /**
     * Возвращает массив последних курсов упорядоченых по дате получения
     * @return Currency[]
     */
    private function getRawData()
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

    /**
     * Форматирует данные полученные из БД в массив формата JS библиотеки
     * @param Currency[] $rawData
     * @return array
     */
    private function graphFormat(array $rawData)
    {
        return array_map(
            function(Currency $currency) {
                return [
                    'x' => $currency->getTime()->format(\DateTime::ATOM),
                    'y' => $currency->getValue(),
                    'group' => $currency->getType(),
                ];
            },
            $rawData
        );
    }
}
