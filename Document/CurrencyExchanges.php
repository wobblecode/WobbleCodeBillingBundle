<?php

namespace WobbleCode\BillingBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TODO need refactorize
 *
 * @MongoDB\Document()
 */
class CurrencyExchanges
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
     *      min = "0",
     *      max = "999999.999999"
     * )
     */
    protected $ratio;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $date;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "3"
     * )
     */
    protected $finalCurrency;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = "3"
     * )
     */
    protected $baseCurrency;

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
     * Set ratio
     *
     * @param float $ratio
     * @return CurrencyExchanges
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * Get ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return CurrencyExchanges
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set finalCurrency
     *
     * @param string $finalCurrency
     * @return CurrencyExchanges
     */
    public function setFinalCurrency($finalCurrency)
    {
        $this->finalCurrency = $finalCurrency;

        return $this;
    }

    /**
     * Get finalCurrency
     *
     * @return string
     */
    public function getFinalCurrency()
    {
        return $this->finalCurrency;
    }

    /**
     * Set baseCurrency
     *
     * @param string $baseCurrency
     * @return CurrencyExchanges
     */
    public function setBaseCurrency($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    /**
     * Get baseCurrency
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }
}
