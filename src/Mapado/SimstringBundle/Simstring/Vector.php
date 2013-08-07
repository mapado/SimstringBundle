<?php

namespace Mapado\SimstringBundle\Simstring;

class Vector implements \Iterator, \Countable
{
    /**
     * vector
     * 
     * @var \Simstring_StringVector
     * @access private
     */
    private $vector;

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
     * @param \Simstring_StringVector $vector
     * @access public
     * @return void
     */
    public function __construct(\Simstring_StringVector $vector)
    {
        $this->vector = $vector;
        $this->position = 0;
    }

    /**
     * @See \Iterator
     */
    public function current ()
    {
        return $this->vector->get($this->position);
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
        return $this->vector->size() > $this->position;
    }

    /**
     * @See \Countable
     */
    public function count()
    {
        return $this->vector->size();
    }
}
