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

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var array
     */
    private $addDiffGraphsCurrencies;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array|null
     */
    private $groups;

    /**
     * @var array|null
     */
    private $items;


    /**
     * @param EntityRepository $entityRepository
     * @param array $addDiffGraphsCurrencies если нужно построить дополнительные графики разницы значений - их нужно передать в массиве
     * @param int $limit
     */
    public function __construct(EntityRepository $entityRepository, $addDiffGraphsCurrencies = [], $limit = 0)
    {
        $this->entityRepository = $entityRepository;
        $this->addDiffGraphsCurrencies = $addDiffGraphsCurrencies;
        $this->limit = $limit;

    }

    /**
     * Возвращает элементы в json формате необходимом для JS библиотеки
     * @return string
     */
    public function getItems()
    {
        if (is_null($this->items)) {
            $rawData = $this->getRawData();

            if (!empty($this->addDiffGraphsCurrencies)) {
                $rawData = $this->addDiffGraphs($rawData);
            }

            $formattedData = $this->graphFormat($rawData);
            $this->items = $formattedData;
        }

        return $this->items;
    }

    /**
     * Возвращает массив групп в формате необходимом для JS библиотеки
     * @return array
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

            foreach ($this->addDiffGraphsCurrencies as $diffCurrencies) {
                $result[] = $this->makeDiffCurrencyType($diffCurrencies[0], $diffCurrencies[1]);
            }

            $this->groups = $result;
        }

        return $this->groups;
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
     * Добавляет данные для еще одного графика - разницы значений
     * @param Currency[] $data
     * @return array
     * @throws \Exception в случае ошибки
     */
//    TODO: Вынести на отдельный график
    private function addDiffGraphs(array $data)
    {
        $result = $data;

        foreach ($this->addDiffGraphsCurrencies as $currencies) {
            if (count($currencies) !== 2) {
                throw new \Exception('You can only build diff graph based on two currencies');
            }

            list($topCurrencyName, $bottomCurrencyName) = $currencies;

            foreach ($data as $currentCurrency) {
                if ($currentCurrency->getType() === $topCurrencyName) {
                    $bottomCurrency = $this->findCurrencyWithTheSameTime($data, $currentCurrency, $bottomCurrencyName);

                    if ($bottomCurrency) {
                        $diffCurrency = new Currency();

                        $diffCurrency->setTime($currentCurrency->getTime());
                        $diffCurrency->setType($this->makeDiffCurrencyType($topCurrencyName, $bottomCurrencyName));
                        $diffCurrency->setValue($currentCurrency->getValue() - $bottomCurrency->getValue());

                        $result[] =$diffCurrency;
                    }
                }
            }
        }

        return $result;
    }

    private function makeDiffCurrencyType($topCurrencyName, $bottomCurrencyName)
    {
        return 'Difference: ' . $topCurrencyName . ' - ' . $bottomCurrencyName;
    }

    /**
     * Находит
     * @param Currency[] $rawData
     * @param Currency $diffCurrency
     * @param string $findCurrencyName
     * @param int $timeDiffInSeconds
     * @return Currency|false
     */
    private function findCurrencyWithTheSameTime(array $rawData, Currency $diffCurrency, $findCurrencyName, $timeDiffInSeconds = 3)
    {
        /** @var Currency $currencyCurrency */
        $currencyCurrency = reset($rawData);

        while ($currencyCurrency) {
            if ($currencyCurrency->getType() === $findCurrencyName) {
                $timeInterval = abs($currencyCurrency->getTime()->getTimestamp() - $diffCurrency->getTime()->getTimestamp());

                if ($timeInterval <= $timeDiffInSeconds) {
                    return $currencyCurrency;
                }
            }

            $currencyCurrency = next($rawData);
        }

        return false;
    }

    /**
     * Добавляет подписи последним курсам каждого из типов
     * @param array $data
     * @return array
     */
    private function addLabelsToLastItems(array $data)
    {
//        Меняем местами ключи и значения, чтобы получить удобный доступ к уникальным значениям групп
        $groups = array_flip($this->getGroups());

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
