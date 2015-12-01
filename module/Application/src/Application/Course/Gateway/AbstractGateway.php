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
            $result['type'] = $this->getShortClassName();
        }

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
    private function getShortClassName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }

    /**
     * Получает результат с соответствующего ресурса
     * @return array
     * @throws \Exception в случае ошибки
     */
    abstract protected function handleGet();
}
