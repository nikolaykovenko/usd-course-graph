<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 01.12.15
 */

namespace Application\FactoryService\Repository;

use Application\Course\Gateway\Interbank;
use Application\Course\Gateway\KursComUaBlackMarket;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CourseGraphPresenter
 * @package Application\FactoryService\Repository
 */
class CourseGraphPresenter implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $courseRepository = $serviceLocator->get('EntityManager')->getRepository('Application\Entity\Currency');

        $presenter = new \Application\Repository\CourseGraphPresenter(
            $courseRepository,
            [
                [KursComUaBlackMarket::NAME, Interbank::NAME],
            ]
        );

        return $presenter;
    }
}
