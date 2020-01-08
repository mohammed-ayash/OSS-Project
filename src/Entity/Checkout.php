<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Checkout
 *
 * @ORM\Table(name="checkout")
 * @ORM\Entity
 */
class Checkout
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime", nullable=false)
     */
    private $createAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="payment", type="boolean", nullable=false)
     */
    private $payment;


}
