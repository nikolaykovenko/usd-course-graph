<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Course;

use Application\Course\Gateway\AbstractGateway;
use Application\Entity\Currency;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Класс для получения и сохранения курсов в БД
 * @package Application\Course
 */
class Getter
{

    /**
     * @var array массив gateways для получения курсов из различных источников
     */
    private $gateways = [];

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Добавляет gateway для получения курса из отдельного источника
     * @param string $gatewayName
     * @param AbstractGateway $gateway
     * @param string $currencyEntityClass
     */
    public function addGateway($gatewayName, AbstractGateway $gateway, $currencyEntityClass)
    {
        $this->gateways[$gatewayName] = [
            'gateway' => $gateway,
            'entityClass' => $currencyEntityClass,
        ];
    }

    /**
     * Возвращает массив курсов полученных со всех инициализированных gateway's
     * @return Currency[]
     */
    public function getFromAllGateways()
    {
        $result = [];

        foreach ($this->gateways as $gatewayArray) {
            $result[] = $this->get($gatewayArray['gateway'], $gatewayArray['entityClass']);
        }

        return $result;
    }

    /**
     * @param $gatewayName
     * @return Currency
     * @throws \Exception
     */
    public function getFromGateway($gatewayName)
    {
        if (!isset($this->gateways[$gatewayName])) {
            throw new \Exception('Gateway ' . $gatewayName . ' not found');
        }

        return $this->get($this->gateways[$gatewayName]['gateway'], $this->gateways[$gatewayName]['entityClass']);
    }



    /**
     * Получает данные о курсе через gateway и возвращает гидрированную entity
     * @param AbstractGateway $gateway
     * @param string $entityClass
     * @return Currency
     */
    private function get(AbstractGateway $gateway, $entityClass)
    {
        $result = $gateway->get();

        $entity = new $entityClass();
        $this->hydrator->hydrate($result, $entity);

        return $entity;
    }
}
