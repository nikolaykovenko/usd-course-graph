<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 02.12.15
 */

namespace Application\Repository;

use Application\Entity\Currency;

/**
 * Class AbstractGraphPresenter
 * @package Application\Repository
 */
abstract class AbstractGraphPresenter
{

    /**
     * @var array|null
     */
    private $items;

    /**
     * Возвращает элементы в json формате необходимом для JS библиотеки
     * @return array
     */
    public function getItems()
    {
        if (is_null($this->items)) {
            $rawData = $this->getRawData();

            $formattedData = $this->graphFormat($rawData);
            $this->items = $formattedData;
        }

        return $this->items;
    }

    /**
     * Возвращает массив групп в формате необходимом для JS библиотеки
     * @return array
     */
    abstract public function getGroups();

    /**
     * Возвращает данные в сыром виде
     * @return Currency[]
     */
    abstract public function getRawData();


    /**
     * Форматирует данные полученные из БД в массив формата JS библиотеки
     * @param Currency[] $rawData
     * @return array
     */
    protected function graphFormat(array $rawData)
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
    protected function addLabelsToLastItems(array $data)
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
