<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\FactoryService;

use Application\Course\Getter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CourseGetter
 * @package Application\FactoryService
 */
class CourseGetter implements FactoryInterface
{

    /**
     * @var array
     */
    private $gateways = [
        'cashless.privat' => [
            'gatewayClass' => 'Application\Course\Gateway\PrivatCashless',
            'entityClass' => 'Application\Entity\Currency',
        ],
        'black.kurs.com.ua' => [
            'gatewayClass' => 'Application\Course\Gateway\KursComUaBlackMarket',
            'entityClass' => 'Application\Entity\Currency',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $courseGetter = new Getter(new DoctrineObject($serviceLocator->get('EntityManager')));
        $this->initialize($serviceLocator, $courseGetter);

        return $courseGetter;
    }


    /**
     * Инициализирует Getter
     * @param ServiceLocatorInterface $serviceLocator
     * @param Getter $courseGetter
     * @return Getter
     */
    private function initialize(ServiceLocatorInterface $serviceLocator, Getter $courseGetter)
    {
        foreach ($this->gateways as $gatewayName => $gatewayArray) {
            $courseGetter->addGateway($gatewayName, $serviceLocator->get($gatewayArray['gatewayClass']), $gatewayArray['entityClass']);
        }

        return $courseGetter;
    }
}
