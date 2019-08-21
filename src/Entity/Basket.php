<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketRepository")
 */
class Basket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"basket", "Default"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"basket", "Default"})
     */  
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductPurchase", mappedBy="basket", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"basket", "Default"})
     */
    private $productPurchases;
    
    

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->productPurchases = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|ProductPurchase[]
     */
    public function getProductPurchase(): Collection
    {
        return $this->productPurchases;
    }

    public function addProductPurchase(ProductPurchase $productPurchase): self
    {
        if (!$this->productPurchases->contains($productPurchase)) {
            $this->productPurchases[] = $productPurchase;
            $productPurchase->setBasket($this);
        }

        return $this;
    }

    public function removeProductPurchase(ProductPurchase $productPurchase): self
    {
        if ($this->productPurchases->contains($productPurchase)) {
            $this->productPurchases->removeElement($productPurchase);
            // set the owning side to null (unless already changed)
            if ($productPurchase->getBasket() === $this) {
                $productPurchase->setBasket(null);
            }
        }

        return $this;
    }
}
