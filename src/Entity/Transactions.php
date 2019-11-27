<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM,
    JMS\Serializer\Annotation as Serializer;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions", indexes={@ORM\Index(name="credit_user_id", columns={"credit_user_id"}), @ORM\Index(name="debet_user_id", columns={"debet_user_id"}), @ORM\Index(name="created_at", columns={"created_at"})})
 * @ORM\Entity
 */
class Transactions
{
    /**
     * @var int
     *
     * @Serializer\Exclude()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summ", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $summ;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     *
     * @Serializer\Type("DateTime<'d/m/Y H:i'>")
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="credit_user_id", referencedColumnName="id")
     * })
     *
     * @Serializer\Exclude()
     */
    private $creditUser;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="debet_user_id", referencedColumnName="id")
     * })
     *
     * @Serializer\Exclude()
     */
    private $debetUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSumm(): ?string
    {
        return $this->summ;
    }

    public function setSumm(string $summ): self
    {
        $this->summ = $summ;

        return $this;
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

    public function getCreditUser(): ?Users
    {
        return $this->creditUser;
    }

    public function setCreditUser(?Users $creditUser): self
    {
        $this->creditUser = $creditUser;

        return $this;
    }

    public function getDebetUser(): ?Users
    {
        return $this->debetUser;
    }

    public function setDebetUser(?Users $debetUser): self
    {
        $this->debetUser = $debetUser;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("toUser")
     *
     * @return string
     */
    public function getCreditUserName()
    {
        return $this->creditUser->getUserName();
    }
}
