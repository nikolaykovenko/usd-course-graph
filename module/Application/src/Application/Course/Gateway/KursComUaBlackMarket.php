<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Course\Gateway;

/**
 * Черный рынок на сайте kurs.com.ua
 * @package Application\Course\Gateway
 */
class KursComUaBlackMarket extends AbstractGateway
{

    /**
     * Получает результат с соответствующего ресурса
     * @return array
     */
    protected function handleGet()
    {
        return [
            'value' => 25 + rand(0, 1),
        ];
    }
}
