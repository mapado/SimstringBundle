<?php

namespace Mapado\SimstringBundle\Model;

class SimstringResult
{
    /**
     * threshold
     *
     * @var mixed
     * @access private
     */
    private $threshold;

    /**
     * value
     *
     * @var mixed
     * @access private
     */
    private $value;

    /**
     * __construct
     *
     * @param mixed $value
     * @param float|null $threshold
     * @access public
     * @return void
     */
    public function __construct($value, $threshold = null)
    {
        $this->setValue($value);
        $this->setThreshold($threshold);
    }

    /**
     * Gets the value of threshold
     *
     * @return float
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * Sets the value of threshold
     *
     * @param float $threshold threshold
     *
     * @return SimstringResult
     */
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;
        return $this;
    }

    /**
     * Gets the value of value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value of value
     *
     * @param mixed $value value
     *
     * @return SimstringResult
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
