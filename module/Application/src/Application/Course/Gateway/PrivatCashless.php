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
class PrivatCashless extends AbstractGateway
{

    /**
     * Получает результат с соответствующего ресурса
     * @return array
     */
    protected function handleGet()
    {
        return [
            'value' => 24,
        ];
    }
}
