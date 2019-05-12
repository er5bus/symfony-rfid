<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
    const IN_PENDING = 'in pending';
    const FULLY_ACCEPTED = 'fully accepted';
    const PARTIALLY_ACCEPTED = 'partially accepted';
    const REFUSED = 'refused';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startBorrowingDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endBorrowingDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requestedQuantity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $borrowedQuantity;

    /**
     * @ORM\Column(name="book_status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var Book
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="reservations")
     */
    private $book;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations")
     */
    private $user;

    /**
     * Reservation constructor.
     */
    public function __construct()
    {
        $this->requestedQuantity = 1;
        $this->endBorrowingDate = new DateTime('+ 1 month');
        $this->startBorrowingDate = new DateTime('now');
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist(){
        if (is_null($this->createdAt)){
            $this->createdAt = new DateTime('now');
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function PreUpdate(){
        $this->updatedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getborrowedQuantity(): ?int
    {
        return $this->borrowedQuantity;
    }

    public function setborrowedQuantity(?int $borrowedQuantity): self
    {
        $this->borrowedQuantity = $borrowedQuantity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRequestedQuantity(): ?int
    {
        return $this->requestedQuantity;
    }

    public function setRequestedQuantity(?int $requestedQuantity): self
    {
        $this->requestedQuantity = $requestedQuantity;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
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

    /**
     * @return DateTime
     */
    public function getStartBorrowingDate()
    {
        return $this->startBorrowingDate;
    }

    /**
     * @param mixed $startBorrowingDate
     * @return Reservation
     */
    public function setStartBorrowingDate($startBorrowingDate)
    {
        $this->startBorrowingDate = $startBorrowingDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndBorrowingDate()
    {
        return $this->endBorrowingDate;
    }

    /**
     * @param mixed $endBorrowingDate
     * @return Reservation
     */
    public function setEndBorrowingDate($endBorrowingDate)
    {
        $this->endBorrowingDate = $endBorrowingDate;
        return $this;
    }
}
