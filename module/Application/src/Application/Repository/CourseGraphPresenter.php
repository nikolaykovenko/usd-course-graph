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
     * @var array|null
     */
    private $groupsArray;

    /**
     * @var string|null
     */
    private $itemsJson;


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
        if (is_null($this->itemsJson)) {
            $rawData = $this->getRawData();
            $formattedData = $this->graphFormat($rawData);
            $this->itemsJson = json_encode($formattedData);
        }

        return $this->itemsJson;
    }

    /**
     * Возвращает группы в json формате необходимом для JS библиотеки
     * @return string
     */
    public function getGroups()
    {
        return json_encode($this->getGroupsArray());
    }

    /**
     * @return array
     */
    private function getGroupsArray()
    {
        if (is_null($this->groupsArray)) {
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

            $this->groupsArray = $result;
        }

        return $this->groupsArray;
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
        $result = array_map(
            function(Currency $currency) {
                return [
                    'x' => $currency->getTime()->format(\DateTime::ATOM),
                    'y' => $currency->getValue(),
                    'group' => $currency->getType(),
                ];
            },
            $rawData
        );

        return $this->addLabelsToLastItems($result);
    }

    /**
     * Добавляет подписи последним курсам каждого из типов
     * @param array $data
     * @return array
     */
    private function addLabelsToLastItems(array $data)
    {
//        Меняем местами ключи и значения, чтобы получить удобный доступ к уникальным значениям групп
        $groups = array_flip($this->getGroupsArray());

        $item = end($data);
        while (!empty($groups) && $item) {
            $groupName = $item['group'];

            if (isset($groups[$groupName])) {
//                Добавляем текущему элементу label
                $data[key($data)]['label'] = [
                    'content' => $item['y'],
                    'xOffset' => -60,
                    'yOffset' => -10,
                ];

                unset($groups[$groupName]);
            }

            $item = prev($data);
        }

        return $data;
    }
}
