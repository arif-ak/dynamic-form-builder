<?php

namespace App\Entity;

use App\Repository\DynamicFormRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DynamicFormRepository::class)
 */
class DynamicForm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uniqueId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regularPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salesPrice;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(?string $uniqueId): self
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    public function getRegularPrice(): ?string
    {
        return $this->regularPrice;
    }

    public function setRegularPrice(?string $regularPrice): self
    {
        $this->regularPrice = $regularPrice;

        return $this;
    }

    public function getSalesPrice(): ?string
    {
        return $this->salesPrice;
    }

    public function setSalesPrice(string $salesPrice): self
    {
        $this->salesPrice = $salesPrice;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
