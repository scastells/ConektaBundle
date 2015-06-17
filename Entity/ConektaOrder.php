<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 16:02
 */

namespace Scastells\ConektaBundle\Entity;

use BaseEcommerce\Bundles\Core\CoreBundle\Entity\Abstracts\AbstractEntity;
use BaseEcommerce\Bundles\Core\PurchaseBundle\Entity\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConektaOrder
 *
 * @package Scastells\ConektaBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="conekta_order")
 */
class ConektaOrder extends AbstractEntity
{
    /**
     * @var Order $order
     *
     * @ORM\ManyToOne(targetEntity="BaseEcommerce\Bundles\Core\PurchaseBundle\Entity\Order")
     */
    protected $order;

    /**
     * @var string $orderId
     *
     * @ORM\Column(name="conekta_id", type="string", length=255, nullable=true)
     */
    protected $conektaId;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * @var string $failure_code
     *
     * @ORM\Column(name="failure_code", type="string", length=255, nullable=true)
     */
    protected $failureCode;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getConektaId()
    {
        return $this->conektaId;
    }

    /**
     * @param mixed $conektaId
     */
    public function setConektaId($conektaId)
    {
        $this->conektaId = $conektaId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getFailureCode()
    {
        return $this->failureCode;
    }

    /**
     * @param mixed $failureCode
     */
    public function setFailureCode($failureCode)
    {
        $this->failureCode = $failureCode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}