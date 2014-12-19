<?php

namespace Mapado\SimstringBundle\Database;

use Mapado\SimstringBundle\Simstring;

class SimstringClient implements ClientInterface
{
    /**
     * reader
     *
     * @var \Simstring_reader
     * @access private
     */
    private $reader;

    /**
     * minResults
     *
     * @var int
     * @access private
     */
    private $minResults;

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

        if (isset($config['min_results'])) {
            $this->minResults = $config['min_results'];
        } else {
            $this->minResults = 1;
        }
    }

    /**
     * find
     *
     * @param string $query
     * @access public
     * @return \Iterator<\Mapado\SimstringBundle\Model\SimstringResult>
     */
    public function find($query, $threshold = null, $minThreshold = null, $gap = 0.1)
    {
        $searchList = new Simstring\Vector();
        if ($threshold === null || $minThreshold === null) {
            return $this->findThreshold($searchList, $query, $threshold);
        }

        // treat gap error
        $gap = abs($gap);
        if ($gap <= 0) {
            throw new \InvalidArgumentException('gap must be > 0');
        }

        do {
            $searchList = $this->findThreshold($searchList, $query, $threshold);
            $threshold -= $gap;
        } while (count($searchList) < $this->minResults && $threshold > $minThreshold);

        return $searchList;
    }

    /**
     * findThreshold
     *
     * @param Simstring\Vector $searchList
     * @param string $query
     * @param float $threshold
     * @access private
     * @return Simstring\Vector
     */
    private function findThreshold(
        Simstring\Vector $searchList,
        $query,
        $threshold
    ) {
        if ($threshold !== null) {
            $this->reader->threshold = $threshold;
        }

        $searchList->mergeVector($this->reader->retrieve($query), $threshold);

        return $searchList;
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
