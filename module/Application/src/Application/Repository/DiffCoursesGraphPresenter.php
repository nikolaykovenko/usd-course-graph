<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 02.12.15
 */

namespace Application\Repository;

use Application\Entity\Currency;

/**
 * Class DiffCoursesGraphPresenter
 * @package Application\Repository
 */
class DiffCoursesGraphPresenter extends AbstractGraphPresenter
{

    /**
     * @var AbstractGraphPresenter
     */
    private $mainGraphPresenter;

    /**
     * @var array
     */
    private $diffGraphsCurrencies;

    /**
     * @var Currency[]|null
     */
    private $rawData;


    /**
     * @param AbstractGraphPresenter $mainGraphPresenter
     * @param array $diffGraphsCurrencies массив названий курсов, для которых нужно строить графики разницы
     * @throws \Exception в случае ошибки
     */
    public function __construct(AbstractGraphPresenter $mainGraphPresenter, array $diffGraphsCurrencies = [])
    {
        $this->mainGraphPresenter = $mainGraphPresenter;


        foreach ($diffGraphsCurrencies as $currencies) {
            if (count($currencies) !== 2) {
                throw new \Exception('You can only build diff graph based on two currencies');
            }
        }
        $this->diffGraphsCurrencies = $diffGraphsCurrencies;
    }

    /**
     * @inheritdoc
     */
    public function getRawData()
    {
        if (is_null($this->rawData)) {
            $result = [];
            $mainData = $this->mainGraphPresenter->getRawData();

            foreach ($this->diffGraphsCurrencies as $currencies) {

                list($topCurrencyName, $bottomCurrencyName) = $currencies;

                foreach ($mainData as $currentCurrency) {
                    if ($currentCurrency->getType() === $topCurrencyName) {
                        $bottomCurrency = $this->findCurrencyWithTheSameTime($mainData, $currentCurrency, $bottomCurrencyName);

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

            $this->rawData = $result;
        }

        return $this->rawData;
    }

    /**
     * @inheritdoc
     */
    public function getGroups()
    {
        return array_map(
            function($currencies) {
                return $this->makeDiffCurrencyType($currencies[0], $currencies[1]);
            },
            $this->diffGraphsCurrencies
        );
    }



    /**
     * Формирует название группы разницы курсов
     * @param string $topCurrencyName
     * @param string $bottomCurrencyName
     * @return string
     */
    private function makeDiffCurrencyType($topCurrencyName, $bottomCurrencyName)
    {
        return 'Difference: ' . $topCurrencyName . ' - ' . $bottomCurrencyName;
    }

    /**
     * Ищет в сырых данных нужный курс валют полученных в то же время что и образец
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
}
