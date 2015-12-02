<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 02.12.15
 */

namespace Application\FactoryService\Repository;

use Application\Course\Gateway\Interbank;
use Application\Course\Gateway\KursComUaBlackMarket;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DiffCoursesGraphPresenter
 * @package Application\FactoryService\Repository
 */
class DiffCoursesGraphPresenter implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new \Application\Repository\DiffCoursesGraphPresenter(
            $serviceLocator->get('CourseGraphPresenter'),
            [
                [KursComUaBlackMarket::NAME, Interbank::NAME],
            ]
        );
    }
}
