<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Course\Gateway;

/**
 * Class AbstractGateway
 * @package Application\Course\Gateway
 */
abstract class AbstractGateway
{

    CONST NAME = 'abstract';

    /**
     * @var array обязательные поля полученные через gateway
     */
    private $requiredFields = [
        'value',
    ];

    /**
     * Возвращает полученный результат
     * @return array
     * @throws \Exception в случае ошибки
     */
    public function get()
    {
        $result = $this->handleGet();
        if (!$this->validate($result)) {
            throw new \Exception('Internal gateway error');
        }

        if (empty($result['type'])) {
            $result['type'] = $this->getCurrencyType();
        }

        return $result;
    }

    /**
     * Возвращает контент страницы по урлу
     * @param string $url
     * @return string
     */
    protected function getContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.65 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch); // выполняем запрос curl
        curl_close($ch);

        return $result;
    }

    /**
     * Проверяет полученные данные на валидность
     * @param array $data
     * @return bool
     */
    private function validate(array $data)
    {
        $valid = true;
        $field = current($this->requiredFields);

        while ($valid && $field) {
            if (empty($data[$field])) {
                $valid = false;
            }

            $field = next($this->requiredFields);
        }

        return $valid;
    }

    /**
     * Возвращает краткое название класса gateway
     * @return string
     */
    private function getCurrencyType()
    {
        return static::NAME;
    }

    /**
     * Получает результат с соответствующего ресурса
     * @return array
     * @throws \Exception в случае ошибки
     */
    abstract protected function handleGet();
}
