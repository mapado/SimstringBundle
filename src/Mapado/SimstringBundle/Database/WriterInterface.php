<?php
namespace Mapado\SimstringBundle\Database;

interface WriterInterface
{
    /**
     * insert
     *
     * @param array/string $data
     * @access public
     * @return void
     */
    public function insert($data);
}
