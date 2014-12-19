<?php
namespace Mapado\SimstringBundle\Database;

use Mapado\SimstringBundle\DataTransformer;

class SimstringTransformerWriter implements WriterInterface
{
    /**
     * writer
     *
     * @var SimstringWriter
     * @access private
     */
    private $writer;

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
     * @param SimstringWriter $writer
     * @param DataTransformer\TransformerInterface $transformer
     * @access public
     * @return void
     */
    public function __construct(SimstringWriter $writer, DataTransformer\TransformerInterface $transformer)
    {
        $this->writer = $writer;
        $this->transformer = $transformer;
    }

    /**
     * insert
     *
     * @param array/string $data
     * @access public
     * @return void
     */
    public function insert($data)
    {
        if (is_array($data)) {
            $data = \SplFixedArray::fromArray($data);
        }
        if (!$data instanceof \Iterator) {
            $data = \SplFixedArray::fromArray([$data]);
        }
        $transformedList = $this->transformer->transform($data);
        return $this->writer->insert($transformedList);
    }
}
