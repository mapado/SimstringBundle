<?php

namespace Mapado\SimstringBundle\Simstring;

use Mapado\SimstringBundle\Model\SimstringResult;

class Vector implements \Iterator, \Countable
{
    /**
     * itemList
     *
     * @var array
     * @access private
     */
    private $itemList;

    /**
     * position
     *
     * @var int
     * @access private
     */
    private $position;

    /**
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->itemList = [];
        $this->position = 0;
    }

    /**
     * getIterator
     *
     * @access public
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->itemList);
    }

    /**
     * @See \Iterator
     */
    public function current ()
    {
        return $this->itemList[$this->position];
    }

    /**
     * @See \Iterator
     */
    public function key ()
    {
        return $this->position;
    }

    /**
     * @See \Iterator
     */
    public function next ()
    {
        ++$this->position;
    }

    /**
     * @See \Iterator
     */
    public function rewind ()
    {
        $this->position = 0;
    }

    /**
     * @See \Iterator
     */
    public function valid ()
    {
        return isset($this->itemList[$this->position]);
    }

    /**
     * @See \Countable
     */
    public function count()
    {
        return count($this->itemList);
    }

    /**
     * mergeVector
     *
     * @param \Simstring_StringVector $vector
     * @param float $threshold
     * @access public
     * @return Vector
     */
    public function mergeVector(\Simstring_StringVector $vector, $threshold)
    {
        while (!$vector->is_empty()) {
            $value = $vector->pop();
            if (substr($value, 0, 1) === '"') {
                $value = substr($value, 1);
            }
            if (substr($value, -1) === '"') {
                $value = substr($value, 0, -1);
            }

            if (!$this->inItemList($value)) {
                $this->itemList[] = new SimstringResult($value, $threshold);
            }
        }

        return $this;
    }

    /**
     * inItemList
     *
     * @param mixed $value
     * @access private
     * @return bool
     */
    private function inItemList($value)
    {
        foreach ($this->itemList as $item) {
            if ($item->getValue() == $value) {
                return true;
            }
        }

        return false;
    }
}
