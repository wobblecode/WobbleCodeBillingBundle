<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document()
 */
class InvoiceProfile
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * Determines the last id for the public reference
     *
     * @MongoDB\Field(type="int")
     * @Assert\NotBlank()
     */
    protected $refId = 0;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "40"
     * )
     */
    protected $refPattern = "%ref%";

    /**
     * @MongoDB\Field(type="date")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "ui-admin"})
     */
    protected $lastIncrementAt;

    /**
     * @Assert\NotNull()
     * @MongoDB\ReferenceOne(
     *     targetDocument="WobbleCode\UserBundle\Model\OrganizationInterface",
     *     inversedBy="invoceProfile"
     * )
     */
    protected $organization;

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
     * Set refId
     *
     * @param integer $refId
     *
     * @return InvoiceProfile
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Get refId
     *
     * @return integer
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Get refId
     *
     * @return integer
     */
    public function getRenderedRef()
    {
        return str_replace('%ref%', $this->getRefId(), $this->refPattern);
    }

    /**
     * Set refPattern
     *
     * @param string $refPattern
     *
     * @return InvoiceProfile
     */
    public function setRefPattern($refPattern)
    {
        $this->refPattern = $refPattern;

        return $this;
    }

    /**
     * Get refPattern
     *
     * @return string
     */
    public function getRefPattern()
    {
        return $this->refPattern;
    }

    /**
     * Set organization
     *
     * @param WobbleCode\UserBundle\Model\OrganizationInterface $organization
     *
     * @return InvoiceProfile
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
     * Set lastIncrementAt
     *
     * @param date $lastIncrementAt
     * @return self
     */
    public function setLastIncrementAt($lastIncrementAt)
    {
        $this->lastIncrementAt = $lastIncrementAt;
        return $this;
    }

    /**
     * Get lastIncrementAt
     *
     * @return date $lastIncrementAt
     */
    public function getLastIncrementAt()
    {
        return $this->lastIncrementAt;
    }
}
