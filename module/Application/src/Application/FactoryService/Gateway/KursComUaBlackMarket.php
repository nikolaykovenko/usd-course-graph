<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 01.12.15
 */

namespace Application\FactoryService\Gateway;

use Sunra\PhpSimple\HtmlDomParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class KursComUaBlackMarket
 * @package Application\FactoryService\Gateway
 */
class KursComUaBlackMarket implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new \Application\Course\Gateway\KursComUaBlackMarket(new HtmlDomParser());
    }
}
