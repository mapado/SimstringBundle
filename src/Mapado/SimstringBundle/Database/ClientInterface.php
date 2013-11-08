<?php

namespace Mapado\SimstringBundle\Database;

/**
 * ClientInterface
 * 
 * @author Julien Deniau <julien.deniau@mapado.com> 
 */
interface ClientInterface
{
    /**
     * find
     *
     * @param string $query
     * @param float $threshold
     * @access public
     * @return \Iterator
     */
    public function find($query, $threshold = null, $minThreshold = null, $gap = 0.1);
}
