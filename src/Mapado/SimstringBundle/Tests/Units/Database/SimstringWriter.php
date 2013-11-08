<?php

namespace Mapado\SimstringBundle\Tests\Units\Database;

use atoum;
use Mapado\SimstringBundle\Database;

/**
 * SimstringClient
 * 
 * @uses atoum
 * @author Julien Deniau <julien.deniau@mapado.com> 
 */
class SimstringWriter extends atoum
{
    /**
     * testCreate
     *
     * @access public
     * @return void
     */
    public function testCreate()
    {
        // non writing path
        $database = '/non/existing/path.db';
        $this->exception(
            function () use ($database) {
                new Database\SimstringWriter($database);
            }
        );

        // ok
        $database = __DIR__ . '/../../Database/new-create.db';
        $writer = new Database\SimstringWriter($database);
        $writer->insert('line 1');

        // try to read without flushing
        $this->exception(
            function () use ($database) {
                $reader = new Database\SimstringClient($database);
            }
        );

        $writer->flush();


        $reader = $this->getReader($database);
        $this->sizeOf($reader->find('line'))
            ->isEqualTo(1);
    }

    /**
     * testMultipleInsert
     *
     * @access public
     * @return void
     */
    public function testMultipleInsert()
    {
        $database = __DIR__ . '/../../Database/new-multiple.db';
        $writer = new Database\SimstringWriter($database);
        $writer->insert('line 1');
        $writer->insert('line 2');
        $writer->insert(['line 3', 'line 4']);
        $writer->flush();

        //$reader = new Database\SimstringClient($database, $config);
        $reader = $this->getReader($database);
        $this->sizeOf($reader->find('line'))
            ->isEqualTo(4);
    }

    public function testCloseAndRewrite()
    {
        $database = __DIR__ . '/../../Database/new-car.db';
        $writer = new Database\SimstringWriter($database);
        $writer->insert('line 1');
        $writer->flush();

        $writer = new Database\SimstringWriter($database);
        $writer->insert('line 2');
        $writer->flush();

        $reader = $this->getReader($database);
        $this->sizeOf($reader->find('line'))
            ->isEqualTo(1);
    }

    /**
     * getReader
     *
     * @access private
     * @return void
     */
    private function getReader($database)
    {
        $config = ['measure' => 'cosine'];
        return new Database\SimstringClient($database, $config);
    }

    /**
     * tearDown
     *
     * @access public
     * @return void
     */
    public function tearDown()
    {
        @unlink(__DIR__ . '/../../Database/new-create.db');
        @unlink(__DIR__ . '/../../Database/new-create.db.4.cdb');
        @unlink(__DIR__ . '/../../Database/new-multiple.db');
        @unlink(__DIR__ . '/../../Database/new-multiple.db.4.cdb');
        @unlink(__DIR__ . '/../../Database/new-car.db');
        @unlink(__DIR__ . '/../../Database/new-car.db.4.cdb');
    }
}
