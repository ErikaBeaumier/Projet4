<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChoiceRepository")
 */
class Choice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $visit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $halfDay;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(min=1, max=10)
     */
    private $tickets;

    /**
     * @ORM\Column(type="uuid")
     */
    private $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisit(): ?\DateTimeInterface
    {
        return $this->visit;
    }

   public function setVisit(\DateTimeInterface $visit): self
    {
        $this->visit = $visit;

        return $this;
    }


    public function getHalfDay(): ?bool
    {
        return $this->halfDay;
    }

    public function setHalfDay(?bool $halfDay): self
    {
        $this->halfDay = $halfDay;

        return $this;
    }

    public function getTickets(): ?int
    {
        return $this->tickets;
    }

    public function setTickets(int $tickets): self
    {
        $this->tickets = $tickets;

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        
        $metadata->addPropertyConstraint('uuid', new Assert\Uuid());
    
    }
}
