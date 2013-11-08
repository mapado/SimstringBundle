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
class SimstringClient extends atoum
{
    /**
     * testCreate
     *
     * @access public
     * @return void
     */
    public function testCreate()
    {
        // non existing db
        $database = 'not-exiting.db';
        $this->exception(
            function () use ($database) {
                new Database\SimstringClient($database);
            }
        );

        // wrong file
        $database = __DIR__ . '/../../Database/city.csv';
        $this->exception(
            function () use ($database) {
                new Database\SimstringClient($database);
            }
        );

        // working file
        $database = __DIR__ . '/../../Database/city.db';
        $client = new Database\SimstringClient($database);
        $this->object($client)
            ->isInstanceOf('Mapado\SimstringBundle\Database\SimstringClient');

        // working file
        $database = __DIR__ . '/../../Database/city.db';
        $config = [
            'measure' => 'cosine',
            'threshold' => 0.7,
        ];
        $client = new Database\SimstringClient($database, $config);
        $this->object($client)
            ->isInstanceOf('Mapado\SimstringBundle\Database\SimstringClient');
    }

    /**
     * testSearch
     *
     * @access public
     * @return void
     */
    public function testSearch()
    {
        $client = $this->getWorkingClient();

        $resultList = $client->find('paris');
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(1);

        $config = [
            'measure' => 'cosine',
            'threshold' => 0.7,
        ];
        $client = $this->getWorkingClient($config);

        $resultList = $client->find('villeneuve');
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(3);

        // change threshold
        $resultList = $client->find('villrubanne');
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(0);

        $resultList = $client->find('villrubanne', 0.5);
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(1);

        $resultList = $client->find('villrubanne', 1, 0.7);
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(0);

        $resultList = $client->find('villrubanne', 1, 0.5, 0.1);
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo(1);

        
        $this->exception(
            function () use ($client) {
                $client->find('villrubanne', 1, 0.5, 0);
            }
        )->isInstanceOf('\InvalidArgumentException');

    }

    /**
     * getWorkingClient
     *
     * @access private
     * @return void
     */
    private function getWorkingClient($config = [])
    {
        $database = __DIR__ . '/../../Database/city.db';
        $client = new Database\SimstringClient($database, $config);
        return $client;
    }
}
