<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $carrierName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $carrierPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryInformation;


    /**
     * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="command")
     */
    private $orderDetails;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeSessionId;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    public function __toString(): ?string
    {
        return "N°".$this->getReference();
    }

    public function state_to_str(): ?string
    {
        switch ($this->getState()) {
            case 0:
                return "En attente de paiement";
            case 1:
                return "Payée";
            case 2:
                return "En cours de préparation";
            case 3:
                return "Envoyée";
        }
    }

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getTotal()
    {
        $total = null;
        foreach($this->getOrderDetails()->getValues() as $product) {
            $total = $total + ($product->getPrice() * $product->getQuantity());
        }
        return $total + $this->getCarrierPrice();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function carrier_to_str(){
        return $this->getCarrierName() ." - ".$this->getCarrierPrice() * 0.01 ."€";
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): self
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?string
    {
        return $this->carrierPrice;
    }

    public function setCarrierPrice(string $carrierPrice): self
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDeliveryInformation(): ?string
    {
        return $this->deliveryInformation;
    }

    public function setDeliveryInformation(?string $deliveryInformation): self
    {
        $this->deliveryInformation = $deliveryInformation;

        return $this;
    }

    /**
     * @return Collection|OrderDetail[]
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setCommand($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getCommand() === $this) {
                $orderDetail->setCommand(null);
            }
        }

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }
}
