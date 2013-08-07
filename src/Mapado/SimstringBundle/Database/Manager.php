<?php

namespace Mapado\SimstringBundle\Database;

use Mapado\SimstringBundle\Database\SimstringClient;

class Manager
{
    /**
     * databaseList
     * 
     * @var array
     * @access private
     */
    private $databaseList;

    /**
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->databaseList = [];
    }

    /**
     * getdatabase
     *
     * @param mixed $key
     * @access public
     * @return void
     */
    public function getDatabase($key)
    {
        if (isset($this->databaseList[$key])) {
            return $this->databaseList[$key];
        }
        return null;
    }

    /**
     * setdatabase
     *
     * @param string $key
     * @param SimstringClient $database
     * @access public
     * @return void
     */
    public function setDatabase($key, SimstringClient $database)
    {
        $this->databaseList[$key] = $database;
        return $this;
    }
}
