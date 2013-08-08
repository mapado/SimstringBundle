<?php

namespace Mapado\SimstringBundle\Database;

use Mapado\SimstringBundle\DataTransformer;

/**
 * SimstringTransformerClient
 * 
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class SimstringTransformerClient
{
    /**
     * client
     * 
     * @var SimstringClient
     * @access private
     */
    private $client;

    /**
     * transformer
     * 
     * @var DataTransformer\TransformerInterface
     * @access private
     */
    private $transformer;

    /**
     * __construct
     *
     * @param SimstringClient $client
     * @param DataTransformer\TransformerInterface $transformer
     * @access public
     * @return void
     */
    public function __construct(SimstringClient $client, DataTransformer\TransformerInterface $transformer)
    {
        $this->client = $client;
        $this->transformer = $transformer;
    }

    /**
     * find
     *
     * @param string $query
     * @param float $threshold
     * @access public
     * @return Simstring\Vector
     */
    public function find($query, $threshold = null)
    {
        $list = $this->client->find($query, $threshold);
        $transformedList = $this->transformer->reverseTransform($list);
        return $transformedList;
    }
}