<?php

namespace WobbleCode\BillingBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\EmbeddedDocument()
 */
class InvoiceStatement
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @Gedmo\SortableGroup
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"unitary", "general", "taxes"})
     * @Assert\Length(
     *      max = "40"
     * )
     */
    protected $type;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max = "255")
     */
    protected $title;

    /**
     * @MongoDB\Hash
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $amount = ['value' => 0, 'precision' => 4];

    /**
     * @MongoDB\Field(type="int")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = "1",
     *      max = "9999999"
     * )
     */
    protected $units = 1;

    /**
     * @Gedmo\SortablePosition
     * @MongoDB\Field(type="int")
     */
    protected $position;

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
     * Set type
     *
     * @param string $type
     * @return InvoiceStatement
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
     * Set title
     *
     * @param string $title
     * @return InvoiceStatement
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
     * Set units
     *
     * @param float $units
     * @return InvoiceStatement
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get units
     *
     * @return float
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return InvoiceStatement
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
