<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductPurchaseRepository")
 */
class ProductPurchase
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"productPurchase", "Default"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productPurchases")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"productPurchase", "Default"})
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="productPurchases")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"productPurchase", "Default"})
     */
    private $basket;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"productPurchase", "Default"})
     */
    private $quantity;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }
}
