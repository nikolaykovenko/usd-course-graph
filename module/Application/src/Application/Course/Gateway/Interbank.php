<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Course\Gateway;

/**
 * Безналичный курс privat24
 * @package Application\Course\Gateway
 */
class Interbank extends AbstractGateway
{

    CONST NAME = 'interbank.buy';

    private $sourceUrl = 'http://charts.finance.ua/ru/currency/data-daily?for=interbank&source=1&indicator=usd';

    /**
     * @inheritdoc
     */
    protected function handleGet()
    {
        $dataDailyText = $this->getContent($this->sourceUrl);
        $dataDaily = @json_decode($dataDailyText);

        if (empty($dataDaily)) {
            throw new \Exception('Unknown server error');
        }

        $value = $this->findNewestBuyCourse($dataDaily);

        return [
            'value' => $value,
        ];
    }


    /**
     * Находит последний курс покупки в массиве данных
     * @param array $dataDaily
     * @return float
     * @throws \Exception
     */
    private function findNewestBuyCourse(array $dataDaily)
    {
        $newestCourse = end($dataDaily);

        if (empty($newestCourse[1])) {
            throw new \Exception('Course not found in server response');
        }

        return (float) $newestCourse[1];
    }
}
