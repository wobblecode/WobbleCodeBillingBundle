<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use WobbleCode\ManagerBundle\Traits\Document\Attributable;

/**
 * @MongoDB\EmbeddedDocument
 * @Serializer\ExclusionPolicy("all")
 */
class Account
{
    use Attributable;

    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "24"
     * )
     */
    protected $type;

    /**
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "3"
     * )
     */
    protected $currency = 'EUR';

    /**
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
     * @Serializer\Groups({"ui"})
     * @Serializer\Accessor(getter="getTotal", setter="setTotal")
     */
    protected $total = ['value' => 0, 'precision' => 4];

    /**
     * Value of the last positive input (Add Credit/Funds)
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
     * @Serializer\Groups({"ui"})
     * @Serializer\Accessor(getter="getLastPositiveInput", setter="setLastPositiveInput")
     */
    protected $lastPositiveInput = ['value' => 0, 'precision' => 4];

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $lastPositiveInputAt;

    /**
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
     * @Serializer\Accessor(getter="getAvailable", setter="setAvailable")
     */
    protected $available = ['value' => 0, 'precision' => 4];

    /**
     * Unpaid credit
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
     * @Serializer\Accessor(getter="getDebt", setter="setDebt")
     */
    protected $debt = ['value' => 0, 'precision' => 4];

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
     * @MongoDB\ReferenceMany(targetDocument="AccountStatement", mappedBy="account", cascade={"persist"})
     */
    protected $statements;

    public function __construct()
    {
        $this->statements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return 'ID: '.$this->id.' Type: '.$this->type.' Currency: '.$this->currency;
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
     * Set currency
     *
     * @param string $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency
     *
     * @return string $currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return self
     */
    public function setTotal($total)
    {
        $precision = 4;

        $this->total = [
            'value' => intval($total* 10**$precision),
            'precision' => $precision
        ];

        return $this;
    }

    /**
     * Get total
     *
     * @return float $total
     */
    public function getTotal()
    {
        return $this->total['value']/ 10**$this->total['precision'];
    }

    /**
     * setLastPositiveInput
     *
     * @param float $total
     * @return self
     */
    public function setLastPositiveInput($lastPositiveInput)
    {
        $precision = 4;

        $this->lastPositiveInput = [
            'value' => intval($lastPositiveInput* 10**$precision),
            'precision' => $precision
        ];

        return $this;
    }

    /**
     * getLastPositiveInput
     *
     * @return float $total
     */
    public function getLastPositiveInput()
    {
        return $this->lastPositiveInput['value']/ 10**$this->lastPositiveInput['precision'];
    }


    /**
     * Set available
     *
     * @param float $available
     * @return self
     */
    public function setAvailable($available)
    {
        $precision = 4;

        $this->available = [
            'value' => intval($available* 10**$precision),
            'precision' => $precision
        ];

        return $this;
    }

    /**
     * Get available
     *
     * @return float $available
     */
    public function getAvailable()
    {
        return $this->available['value']/ 10**$this->available['precision'];
    }

    /**
     * Set debt
     *
     * @param float $debt
     * @return self
     */
    public function setDebt($debt)
    {
        $precision = 4;

        $this->debt = [
            'value' => intval($debt* 10**$precision),
            'precision' => $precision
        ];

        return $this;
    }

    /**
     * Get debt
     *
     * @return float $debt
     */
    public function getDebt()
    {
        return $this->debt['value']/ 10**$this->debt['precision'];
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
     * Get lastPositiveInput
     *
     * @return date $lastPositiveInput
     */
    public function getLastPositiveInputAt()
    {
        return $this->lastPositiveInput['value']/ 10**$this->lastPositiveInput['precision'];
    }

    /**
     * Set setLastPositiveInputAt
     *
     * @param date $lastPositiveInput
     * @return self
     */
    public function setLastPositiveInputAt($lastPositiveInput)
    {
        $precision = 4;

        $this->available = [
            'value' => intval($lastPositiveInput* 10**$precision),
            'precision' => $precision
        ];

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
     * Add statement
     *
     * @param AccountStatement $statement
     */
    public function addStatement(\WobbleCode\BillingBundle\Document\AccountStatement $statement)
    {
        $this->statements[] = $statement;
    }

    /**
     * Remove statement
     *
     * @param AccountStatement $statement
     */
    public function removeStatement(\WobbleCode\BillingBundle\Document\AccountStatement $statement)
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
}
