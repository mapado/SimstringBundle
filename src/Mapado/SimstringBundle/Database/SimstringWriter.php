<?php
namespace Mapado\SimstringBundle\Database;

class SimstringWriter implements WriterInterface
{
    /**
     * writer
     *
     * @var \Simstring_writer
     * @access private
     */
    private $writer;

    /**
     * __construct
     *
     * @param mixed $database
     * @param array $config
     * @access public
     * @return void
     */
    public function __construct($database, $config = array())
    {
        $this->writer = new \Simstring_writer($database);

        if (isset($config['ngram'])) {
            $this->writer->n = $config['ngram'];
        }

        if (isset($config['be'])) {
            $this->writer->be = $config['be'];
        }

        if (isset($config['unicode'])) {
            $this->writer->unicode = $config['unicode'];
        }
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
        if (is_scalar($data)) {
            return $this->insertOne($data);
        } elseif (!empty($data)) {
            foreach ($data as $tmp) {
                $this->insertOne($tmp);
            }
        }
        return $this;
    }

    /**
     * insertOne
     *
     * @param string $line
     * @access private
     * @return SimstringWriter
     */
    private function insertOne($line)
    {
        $this->writer->insert(strtolower($line));
        return $this;
    }

    /**
     * flush
     *
     * @access public
     * @return void
     */
    public function flush()
    {
        $this->writer->close();
    }

    /**
     * __destruct
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->flush();
    }
}
