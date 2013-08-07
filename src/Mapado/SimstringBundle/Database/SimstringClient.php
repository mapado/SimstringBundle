<?php

namespace Mapado\SimstringBundle\Database;

use Mapado\SimstringBundle\Simstring;

class SimstringClient
{
    /**
     * reader
     * 
     * @var \Simstring_reader
     * @access private
     */
    private $reader;

    /**
     * __construct
     *
     * @param string $database database file path
     * @param array $config config
     * @access public
     * @return void
     */
    public function __construct($database, $config = array())
    {
        $this->reader = new \Simstring_reader($database);
        if (isset($config['measure'])) {
            switch ($config['measure']) {
                case 'cosine':
                    $measure = \Simstring_::cosine;
                    break;
                case 'dice':
                    $measure = \Simstring_::dice;
                    break;
                case 'jaccard':
                    $measure = \Simstring_::jaccard;
                    break;
                case 'overlap':
                    $measure = \Simstring_::overlap;
                    break;
                default:
                    $measure = \Simstring_::exact;
                    break;
            }
        } else {
            $measure = \Simstring_::exact;
        }
        $this->reader->measure = $measure;

        if (isset($config['threshold'])) {
            $this->reader->threshold = $config['threshold'];
        }
    }

    /**
     * find
     *
     * @param string $query
     * @access public
     * @return void
     */
    public function find($query, $threshold = null)
    {
        if ($threshold !== null) {
            $this->reader->threshold = $threshold;
        }
        $vector = new Simstring\Vector($this->reader->retrieve($query));
        return $vector;
    }

    /**
     * __destruct
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->reader->close();
    }
}
