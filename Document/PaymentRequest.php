<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class PaymentRequest
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * TODO Define an Exact precision model Eg: {"amount":123305,"precision":4}
     *
     * @MongoDB\Field(type="float")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="float")
     * @Assert\Range(
     *      min="-9999999.999",
     *      max="9999999.999"
     * )
     */
    protected $amount;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="40")
     */
    protected $status;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $details;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
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
     * @MongoDB\ReferenceOne(targetDocument="Invoice", mappedBy="paymentRequest")
     */
    protected $invoice;

    /**
     * @MongoDB\ReferenceOne(targetDocument="PaymentProfile", inversedBy="chargeRequest")
     */
    protected $paymentProfile;

    /**
     * @MongoDB\ReferenceOne(targetDocument="AccountStatement", mappedBy="chargeRequest")
     */
    protected $accountStatement;

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
     * Set amount
     *
     * @param float $amount
     * @return PaymentRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * @return PaymentRequest
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
     * Set accountStatement
     *
     * @param \WobbleCode\BillingBundle\Document\PaymentRequest $accountStatement
     * @return PaymentRequest
     */
    public function setAccountStatement(\WobbleCode\BillingBundle\Document\PaymentRequest $accountStatement = null)
    {
        $this->accountStatement = $accountStatement;

        return $this;
    }

    /**
     * Get accountStatement
     *
     * @return \WobbleCode\BillingBundle\Document\PaymentRequest
     */
    public function getAccountStatement()
    {
        return $this->accountStatement;
    }

    /**
     * Set paymentProfile
     *
     * @param WobbleCode\BillingBundle\Document\PaymentProfile $paymentProfile
     * @return self
     */
    public function setPaymentProfile(\WobbleCode\BillingBundle\Document\PaymentProfile $paymentProfile)
    {
        $this->paymentProfile = $paymentProfile;
        return $this;
    }

    /**
     * Get paymentProfile
     *
     * @return WobbleCode\BillingBundle\Document\PaymentProfile $paymentProfile
     */
    public function getPaymentProfile()
    {
        return $this->paymentProfile;
    }
}
