<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\ExclusionPolicy("all")
 * @MongoDB\Document()
 */
class ChargeRequest
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * draft, canceled, confirmed, executed, fraud
     *
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="40")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $status = 'draft';

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $chargeTitle = 'chargeRequest.chargeTitle';

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     * @Serializer\Groups({"ui", "api"})
     */
    protected $feeTitle = 'chargeRequest.feeTitle';

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     * @Serializer\Groups({"ui", "api"})
     */
    protected $taxTitle = 'chargeRequest.taxTitle';

    /**
     * @MongoDB\Hash
     * @Serializer\Accessor(getter="getAmount")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $amount = ['value' => 0, 'precision' => 4];

    /**
     * TODO Define an Exact precision model Eg: {"amount":123305,"precision":4}
     *
     * @MongoDB\Field(type="float")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Range(
     *      min = "0",
     *      max = "100"
     * )
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $feePercentage = 0;

    /**
     * @MongoDB\Field(type="int")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Range(
     *      min = "0",
     *      max = "100"
     * )
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $taxPercentage = 0;

    /**
     * A base 36 ecnode Id for public reference
     *
     * @var string
     *
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Serializer\ReadOnly
     * @Serializer\Accessor(getter="getPublicReference")
     */
    protected $publicReference;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\Length(max="255")
     */
    protected $externalReference;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $details;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $confirmedAt;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $executedAt;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $canceledAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $updatedAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     */
    protected $updatedBy;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $deletedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Invoice")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $invoice;

    /**
     * @MongoDB\ReferenceOne(targetDocument="PaymentProfile")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $paymentProfile;

    /**
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Model\OrganizationInterface")
     * @Assert\NotNull()
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $organization;

    /**
     * __toString
     *
     * @return string id + order public reference
     */
    public function __toString()
    {
        return 'ID: '.$this->id.' Order: '.$this->getPublicReference();
    }

    /**
     * @return boolean
     */
    public function isConfirmable()
    {
        return in_array($this->status, ['draft', 'canceled', 'pending']);
    }

    /**
     * @return boolean
     */
    public function isExecutable()
    {
        return in_array($this->status, ['draft', 'confirmed', 'pending']);
    }

    /**
     * @return boolean
     */
    public function isCancelable()
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }

    /**
     * @return boolean
     */
    public function isDeletable()
    {
        return in_array($this->status, ['draft', 'confirmed', 'canceled']);
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
     * Set Amount
     *
     * @param float $amount
     */
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
     * Set feePercentage
     *
     * @param string $feePercentage
     * @return ChargeRequest
     */
    public function setFeePercentage($feePercentage)
    {
        $this->feePercentage = $feePercentage;

        return $this;
    }

    /**
     * Get feePercentage
     *
     * @return float
     */
    public function getFeePercentage()
    {
        return $this->feePercentage;
    }

    /**
     * Set taxPercentage
     *
     * @param float $taxPercentage
     * @return ChargeRequest
     */
    public function setTaxPercentage($taxPercentage)
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    /**
     * Get taxPercentage
     *
     * @return float
     */
    public function getTaxPercentage()
    {
        return $this->taxPercentage;
    }

    /**
     * Get breakdown fee amount
     *
     * @return float
     */
    public function getFeeAmount()
    {
        return $this->getAmount() * $this->getFeePercentage() / 100;
    }

    /**
     * Tax base: amount + fee
     *
     * @return float
     */
    public function getTaxBase()
    {
        return $this->getAmount() + $this->getFeeAmount();
    }

    /**
     * Tax decoupled detail
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->getTaxBase() * $this->getTaxPercentage() / 100;
    }

    /**
     * Final amount: amount + fee + taxes
     *
     * @return float
     */
    public function getFinalAmount()
    {
        return $this->getTaxBase() + $this->getTaxAmount();
    }

    /**
     * Get amount
     *
     * @return string Id base convert to 36
     */
    public function getPublicReference()
    {
        return strtoupper(base_convert(hexdec($this->id), 10, 36));
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ChargeRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return ChargeRequest
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set canceledAt
     *
     * @param \DateTime $canceledAt
     * @return ChargeRequest
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * Get canceledAt
     *
     * @return \DateTime
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     * @return ChargeRequest
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Set executedAt
     *
     * @param \DateTime $executedAt
     * @return ChargeRequest
     */
    public function setExecutedAt($executedAt)
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    /**
     * Get executedAt
     *
     * @return \DateTime
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChargeRequest
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
     * @return ChargeRequest
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
     * Set createdBy
     *
     * @param \WobbleCode\UserBundle\Document\User $createdBy
     * @return ChargeRequest
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
     * @return ChargeRequest
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
     * Set invoice
     *
     * @param \WobbleCode\BillingBundle\Document\Invoice $invoice
     * @return ChargeRequest
     */
    public function setInvoice(\WobbleCode\BillingBundle\Document\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \WobbleCode\BillingBundle\Document\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set externalReference
     *
     * @param string $externalReference
     * @return ChargeRequest
     */
    public function setExternalReference($externalReference)
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    /**
     * Get externalReference
     *
     * @return string
     */
    public function getExternalReference()
    {
        return $this->externalReference;
    }

    /**
     * Set paymentProfile
     *
     * @param \WobbleCode\BillingBundle\Document\PaymentProfile $paymentProfile
     *
     * @return ChargeRequest
     */
    public function setPaymentProfile(\WobbleCode\BillingBundle\Document\PaymentProfile $paymentProfile = null)
    {
        $this->paymentProfile = $paymentProfile;
        if ($paymentProfile != null) {
            $this->feePercentage = $paymentProfile->getFee();
        }

        return $this;
    }

    /**
     * Get paymentProfile
     *
     * @return \WobbleCode\BillingBundle\Document\PaymentProfile
     */
    public function getPaymentProfile()
    {
        return $this->paymentProfile;
    }

    /**
     * Set organization
     *
     * @param WobbleCode\UserBundle\Model\OrganizationInterface $organization
     *
     * @return ChargeRequest
     */
    public function setOrganization(\WobbleCode\UserBundle\Model\OrganizationInterface $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \WobbleCode\UserBundle\Model\OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set chargeTitle
     *
     * @param string $chargeTitle
     * @return self
     */
    public function setChargeTitle($chargeTitle)
    {
        $this->chargeTitle = $chargeTitle;
        return $this;
    }

    /**
     * Get chargeTitle
     *
     * @return string $chargeTitle
     */
    public function getChargeTitle()
    {
        return $this->chargeTitle;
    }

    /**
     * Set feeTitle
     *
     * @param string $feeTitle
     * @return self
     */
    public function setFeeTitle($feeTitle)
    {
        $this->feeTitle = $feeTitle;
        return $this;
    }

    /**
     * Get feeTitle
     *
     * @return string $feeTitle
     */
    public function getFeeTitle()
    {
        return $this->feeTitle;
    }

    /**
     * Set taxTitle
     *
     * @param string $taxTitle
     * @return self
     */
    public function setTaxTitle($taxTitle)
    {
        $this->taxTitle = $taxTitle;
        return $this;
    }

    /**
     * Get taxTitle
     *
     * @return string $taxTitle
     */
    public function getTaxTitle()
    {
        return $this->taxTitle;
    }
}
