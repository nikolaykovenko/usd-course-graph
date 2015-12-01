<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="currencies")
 */
class Currency
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=4)
     * @var float
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $time;

    function __construct()
    {
        $this->time = new \DateTime();
    }

    /**
     * Возвращает
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Возвращает
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Возвращает
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Устанавливает
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Возвращает
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Устанавливает
     * @param \DateTime $time
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;
    }
}
