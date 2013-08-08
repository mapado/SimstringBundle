<?php

namespace Mapado\SimstringBundle\DataTransformer;

interface TransformerInterface
{
    /**
     * __construct
     *
     * @param mixed $persistenceService
     * @param string $model
     * @param string $field
     * @param array $options
     * @access public
     * @return void
     */
    public function __construct($persistenceService, $model, $field, array $options = []);

    /**
     * reverseTransform
     *
     * @param \Iterator $stringList
     * @access public
     * @return \Iterator Object list
     */
    public function reverseTransform(\Iterator $stringList);
}
