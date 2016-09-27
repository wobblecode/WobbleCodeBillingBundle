<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use WobbleCode\UserBundle\Document\User;
use WobbleCode\UserBundle\Document\Contact;

/**
 * @Serializer\ExclusionPolicy("all")
 * @MongoDB\Document()
 */
class Invoice
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * Type defined if the invoice is received or issued by a wobblecode user
     *
     * @MongoDB\Field(type="string")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\Choice(choices = {"received", "issued"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "40"
     * )
     */
    protected $type;

     /**
     * @MongoDB\Field(type="date")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\DateTime()
     */
    protected $issuedAt;

    /**
     * @MongoDB\Field(type="date")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\DateTime()
     */
    protected $dueAt;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $status;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "40"
     * )
     */
    protected $reference;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "40"
     * )
     */
    protected $hash;

    /**
     * @MongoDB\EmbedMany(targetDocument="InvoiceStatement")
     */
    protected $statements;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Field(type="date")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\DateTime()
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Field(type="date")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     * @Assert\DateTime()
     */
    protected $updatedAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $updatedBy;

    /**
    * @MongoDB\EmbedOne(targetDocument="WobbleCode\UserBundle\Document\Contact")
     */
    protected $issuer;

    /**
    * @MongoDB\EmbedOne(targetDocument="WobbleCode\UserBundle\Document\Contact")
     */
    protected $recipient;

    /**
     * @MongoDB\ReferenceOne(targetDocument="ChargeRequest", inversedBy="invoice")
     */
    protected $chargeRequest;

    /**
     * @MongoDB\ReferenceOne(targetDocument="PaymentRequest", inversedBy="invoice")
     */
    protected $paymentRequest;

    /**
     * @MongoDB\ReferenceOne(targetDocument="PaymentProfile", inversedBy="invoice")
     */
    protected $paymentProfile;

    /**
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Model\OrganizationInterface", inversedBy="invoices")
     * @Assert\NotNull()
     */
    protected $organization;

    /**
     * Constructor, it generates a hash by default
     */
    public function __construct()
    {
        $this->hash = sha1(time().uniqid());
        $this->issuedAt = new \DateTime('now');
        $this->dueAt = new \DateTime('now');
    }

    public function __toString()
    {
        return 'Ref: '.$this->reference.' Organization: '.$this->getOrganization()->getContactName();
    }

    /**
     * Get Statements by type
     *
     * @return float
     */
    public function getStatementsByType($type)
    {
        $statements = $this->getStatements();
        $filteredStatements = [];

        foreach ($statements as $statement) {
            if ($statement->getType() == $type) {
                $filteredStatements[] = $statement;
            }
        }

        return $filteredStatements;
    }

    /**
     * Get total amount of unitary statements
     *
     * @return float
     */
    public function getUnitarySum()
    {
        $statements = $this->getStatements();
        $total = [];

        foreach ($statements as $statement) {
            if ($statement->getType() == 'unitary') {
                $total[] = $statement->getAmount();
            }
        }

        return array_sum($total);
    }

    /**
     * Get total amount of general statements
     *
     * @return float
     */
    public function getGeneralSum()
    {
        $unitarySum = $this->getUnitarySum();
        $statements = $this->getStatements();
        $total = [];

        foreach ($statements as $statement) {
            if ($statement->getType() == 'general') {
                $total[] = $statement->getAmount() * $unitarySum / 100;
            }
        }

        return array_sum($total);
    }

    /**
     * Get total taxes amount
     *
     * @return float
     */
    public function getTaxesSum()
    {
        $totalSum = $this->getTotal();
        $statements = $this->getStatements();
        $taxes = [];

        foreach ($statements as $statement) {
            if ($statement->getType() == 'taxes') {
                $taxes[] = $statement->getAmount() * $totalSum / 100;
            }
        }

        return array_sum($taxes);
    }

    /**
     * Get total withouh taxes
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("total")
     * @Serializer\Groups({"ui", "api"})
     *
     * @return float
     */
    public function getTotal()
    {
        return array_sum([
            $this->getUnitarySum(),
            $this->getGeneralSum()
        ]);
    }

    /**
     * Get total with taxes
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("totalWithTaxes")
     * @Serializer\Groups({"ui", "api"})
     *
     * @return float
     */
    public function getTotalWithTaxes()
    {
        return array_sum([
            $this->getTotal(),
            $this->getTaxesSum()
        ]);
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set issuedAt
     *
     * @param date $issuedAt
     * @return self
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * Get issuedAt
     *
     * @return date $issuedAt
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set dueAt
     *
     * @param date $dueAt
     * @return self
     */
    public function setDueAt($dueAt)
    {
        $this->dueAt = $dueAt;
        return $this;
    }

    /**
     * Get dueAt
     *
     * @return date $dueAt
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return self
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Get reference
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get hash
     *
     * @return string $hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param WobbleCode\UserBundle\Document\User $createdBy
     * @return self
     */
    public function setCreatedBy(\WobbleCode\UserBundle\Document\User $createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return WobbleCode\UserBundle\Document\User $createdBy
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param WobbleCode\UserBundle\Document\User $updatedBy
     * @return self
     */
    public function setUpdatedBy(\WobbleCode\UserBundle\Document\User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return WobbleCode\UserBundle\Document\User $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set issuer
     *
     * @param WobbleCode\UserBundle\Document\Contact $issuer
     * @return self
     */
    public function setIssuer(\WobbleCode\UserBundle\Document\Contact $issuer)
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * Get issuer
     *
     * @return WobbleCode\UserBundle\Document\Contact $issuer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Set recipient
     *
     * @param WobbleCode\UserBundle\Document\Contact $recipient
     * @return self
     */
    public function setRecipient(\WobbleCode\UserBundle\Document\Contact $recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * Get recipient
     *
     * @return WobbleCode\UserBundle\Document\Contact $recipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set chargeRequest
     *
     * @param WobbleCode\BillingBundle\Document\ChargeRequest $chargeRequest
     * @return self
     */
    public function setChargeRequest(\WobbleCode\BillingBundle\Document\ChargeRequest $chargeRequest)
    {
        $this->chargeRequest = $chargeRequest;
        return $this;
    }

    /**
     * Get chargeRequest
     *
     * @return WobbleCode\BillingBundle\Document\ChargeRequest $chargeRequest
     */
    public function getChargeRequest()
    {
        return $this->chargeRequest;
    }

    /**
     * Set paymentRequest
     *
     * @param WobbleCode\BillingBundle\Document\PaymentRequest $paymentRequest
     * @return self
     */
    public function setPaymentRequest(\WobbleCode\BillingBundle\Document\PaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;
        return $this;
    }

    /**
     * Get paymentRequest
     *
     * @return WobbleCode\BillingBundle\Document\PaymentRequest $paymentRequest
     */
    public function getPaymentRequest()
    {
        return $this->paymentRequest;
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

    /**
     * Add statement
     *
     * @param WobbleCode\BillingBundle\Document\InvoiceStatement $statement
     */
    public function addStatement(\WobbleCode\BillingBundle\Document\InvoiceStatement $statement)
    {
        $this->statements[] = $statement;
    }

    /**
     * Remove statement
     *
     * @param WobbleCode\BillingBundle\Document\InvoiceStatement $statement
     */
    public function removeStatement(\WobbleCode\BillingBundle\Document\InvoiceStatement $statement)
    {
        $this->statements->removeElement($statement);
    }

    /**
     * Get statements
     *
     * @return \Doctrine\Common\Collections\Collection $statements
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Set organization
     *
     * @param WobbleCode\UserBundle\Model\OrganizationInterface $organization
     * @return self
     */
    public function setOrganization(\WobbleCode\UserBundle\Model\OrganizationInterface $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Get organization
     *
     * @return WobbleCode\UserBundle\Model\OrganizationInterface $organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
