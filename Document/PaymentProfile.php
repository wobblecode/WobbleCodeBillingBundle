<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document()
 */
class PaymentProfile
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @Gedmo\SortablePosition
     * @MongoDB\Field(type="int")
     */
    private $order;

    /**
     * Defines the key name for the payment Profile method. This is used for the
     * translation keys
     *
     * @MongoDB\Field(type="string")
     */
    protected $key;

    /**
     * Determines if the profile is used for inbound or outbound payments.
     *
     * Eg: if it's inbound and belongs to a wobblecode user. It means that this
     * profile will be used to pay money to this user.
     *
     * If it's an outbound profile. It will be used as a charge profile, like a
     * credit card profile where to charge credits for push sms.
     *
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"inbound", "outbound"})
     */
    protected $type;

    /**
     * It defines the method by a reference name Eg: paypal, wireTransfer,
     * creditCard. This property only defined the method type, not the provider.
     *
     * @MongoDB\Field(type="string")
     */
    protected $method;

    /**
     * It defines if the payment profile is a system method payment used to pay
     * services used in the system. Eg: Load credits to the usser account,
     * request an special support, etc.
     *
     * @MongoDB\Field(type="boolean")
     */
    protected $system;

    /**
     * Whether or not if the profile is available for its use.
     *
     * @MongoDB\Field(type="boolean")
     */
    protected $enabled;

    /**
     * Whether or not if the profile is available for its use.
     *
     * @MongoDB\Field(type="boolean")
     */
    protected $enabledByDefault;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="64")
     */
    protected $title;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    /**
     * An extra percentage fee that will be charged. This will be usually used
     * for extra costs that won't be reflected in the user balance. Eg: The
     * extra cost that applies a payment provider like the fee applied by a TPV.
     *
     * @MongoDB\Field(type="float")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $fee = 0;

    /**
     * Symfony Di service
     *
     * @MongoDB\Field(type="string")
     */
    protected $service;

    /**
     * @MongoDB\Hash
     */
    protected $params = [];

    /**
     * @MongoDB\Hash
     * @Assert\Type(type="array")
     */
    protected $countryRestriction;

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
     * @MongoDB\ReferenceOne(
     *     targetDocument="WobbleCode\UserBundle\Model\OrganizationInterface",
     * )
     * @Assert\NotNull()
     */
    protected $organization;

    /**
     * To String
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
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
     * Set method
     *
     * @param string $method
     * @return self
     */
    public function setMethod($method)
    {
	$this->method = $method;
	return $this;
    }

    /**
     * Get method
     *
     * @return string $method
     */
    public function getMethod()
    {
	return $this->method;
    }

    /**
     * Set system
     *
     * @param boolean $system
     * @return self
     */
    public function setSystem($system)
    {
        $this->system = $system;
        return $this;
    }

    /**
     * Get system
     *
     * @return boolean $system
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set fee
     *
     * @param float $fee
     * @return self
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * Get fee
     *
     * @return float $fee
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set countryRestriction
     *
     * @param hash $countryRestriction
     * @return self
     */
    public function setCountryRestriction($countryRestriction)
    {
        $this->countryRestriction = $countryRestriction;
        return $this;
    }

    /**
     * Get countryRestriction
     *
     * @return hash $countryRestriction
     */
    public function getCountryRestriction()
    {
        return $this->countryRestriction;
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
     * Set service
     *
     * @param string $service
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string $service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set params
     *
     * @param hash $params
     * @return self
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get params
     *
     * @return hash $params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return integer $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set enabledByDefault
     *
     * @param boolean $enabledByDefault
     * @return self
     */
    public function setEnabledByDefault($enabledByDefault)
    {
        $this->enabledByDefault = $enabledByDefault;
        return $this;
    }

    /**
     * Get enabledByDefault
     *
     * @return boolean $enabledByDefault
     */
    public function getEnabledByDefault()
    {
        return $this->enabledByDefault;
    }
}
