<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\ExclusionPolicy("all")
 * @MongoDB\Document()
 * @MongoDB\UniqueIndex(keys={"account.$id"="asc", "hash"="asc"})
 */
class AccountStatement
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "255"
     * )
     */
    protected $title;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\Length(max = "40")
     */
    protected $hash;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\Length(max = "32")
     */
    protected $type;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     */
    protected $description;

    /**
     *
     * Cost applied to client
     *
     * @example
     *
     * {
     *     "value": 12,
     *     "precision": 4
     * }
     *
     * @MongoDB\Hash
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $amount = ['value' => 0, 'precision' => 4];


    /**
     * @MongoDB\Field(type="date")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\DateTime()
     */
    protected $effectiveAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $updatedAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     * @Serializer\Exclude
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     * @Serializer\Exclude
     */
    protected $updatedBy;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $deletedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Account", inversedBy="statements")
     * @Serializer\Exclude
     * @Assert\NotNull()
     */
    protected $account;

    /**
     * @MongoDB\ReferenceOne(targetDocument="ChargeRequest", inversedBy="accountStatement", cascade={"remove"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $chargeRequest;

    /**
     * @MongoDB\ReferenceOne(targetDocument="PaymentRequest", inversedBy="accountStatement", cascade={"remove"})
     */
    protected $paymentRequest;

    /**
     * Constructor
     */
    public function __construct()
    {
        return $this->effectiveAt = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return AccountStatement
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AccountStatement
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return AccountStatement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AccountStatement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setAmount($amount)
    {
        $precision = 4;

        $this->amount = [
            'value' => intval($amount* 10**$precision),
            'precision' => $precision
        ];

        return $this;
    }

    /**
     * Get total
     *
     * @return float $total
     */
    public function getAmount()
    {
        return $this->amount['value']/ 10**$this->amount['precision'];
    }

    /**
     * Set effectiveAt
     *
     * @param \DateTime $effectiveAt
     * @return AccountStatement
     */
    public function setEffectiveAt($effectiveAt)
    {
        $this->effectiveAt = $effectiveAt;

        return $this;
    }

    /**
     * Get effectiveAt
     *
     * @return \DateTime
     */
    public function getEffectiveAt()
    {
        return $this->effectiveAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AccountStatement
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return AccountStatement
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param \WobbleCode\UserBundle\Document\User $createdBy
     * @return AccountStatement
     */
    public function setCreatedBy(\WobbleCode\UserBundle\Document\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \WobbleCode\UserBundle\Document\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \WobbleCode\UserBundle\Document\User $updatedBy
     * @return AccountStatement
     */
    public function setUpdatedBy(\WobbleCode\UserBundle\Document\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \WobbleCode\UserBundle\Document\User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return ChargeRequest
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set account
     *
     * @param \WobbleCode\BillingBundle\Document\Account $account
     * @return AccountStatement
     */
    public function setAccount(\WobbleCode\BillingBundle\Document\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \WobbleCode\BillingBundle\Document\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set chargeRequest
     *
     * @param \WobbleCode\BillingBundle\Document\ChargeRequest $chargeRequest
     * @return AccountStatement
     */
    public function setChargeRequest(\WobbleCode\BillingBundle\Document\ChargeRequest $chargeRequest = null)
    {
        $this->chargeRequest = $chargeRequest;

        return $this;
    }

    /**
     * Get chargeRequest
     *
     * @return \WobbleCode\BillingBundle\Document\ChargeRequest
     */
    public function getChargeRequest()
    {
        return $this->chargeRequest;
    }

    /**
     * Set paymentRequest
     *
     * @param \WobbleCode\BillingBundle\Document\PaymentRequest $paymentRequest
     * @return AccountStatement
     */
    public function setPaymentRequest(\WobbleCode\BillingBundle\Document\PaymentRequest $paymentRequest = null)
    {
        $this->paymentRequest = $paymentRequest;

        return $this;
    }

    /**
     * Get paymentRequest
     *
     * @return \WobbleCode\BillingBundle\Document\PaymentRequest
     */
    public function getPaymentRequest()
    {
        return $this->paymentRequest;
    }
}
