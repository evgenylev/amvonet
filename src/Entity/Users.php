<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="uq_user_name", columns={"user_name"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=64, nullable=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2, nullable=false, options={"default"="10000.00"})
     */
    private $balance = '10000.00';

    /**
     * @var bool
     *
     * @Serializer\Exclude()
     * @ORM\Column(name="is_active", type="boolean", nullable=false, options={"default"="b'1'"})
     */
    private $isActive = 'b\'1\'';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
