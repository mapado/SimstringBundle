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

        $this->validateResult($resultList, 1);

        $config = [
            'measure' => 'cosine',
            'threshold' => 0.7
        ];
        $client = $this->getWorkingClient($config);

        $resultList = $client->find('villeneuve');
        $this->validateResult($resultList, 2);

        // change threshold
        $resultList = $client->find('villrubanne');
        $this->validateResult($resultList, 0);

        $resultList = $client->find('villrubanne', 0.5);
        $this->validateResult($resultList, 1);

        $resultList = $client->find('villrubanne', 1, 0.7);
        $this->validateResult($resultList, 0);

        $resultList = $client->find('villrubanne', 1, 0.5, 0.1);
        $this->validateResult($resultList, 1);

        // test min results
        $config['min_results'] = 3;
        $client = $this->getWorkingClient($config);
        $resultList = $client->find('villrubanne', 1, 0.2, 0.1);
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isGreaterThan(1);


        $this->exception(
            function () use ($client) {
                $client->find('villrubanne', 1, 0.5, 0);
            }
        )->isInstanceOf('\InvalidArgumentException');

    }

    /**
     * validateResult
     *
     * @param \Iterator $resultList
     * @param int $nb
     * @access private
     * @return void
     */
    private function validateResult($resultList, $nbResults)
    {
        $this->object($resultList)
            ->isInstanceOf('\Iterator');

        $this->sizeOf($resultList)
            ->isEqualTo($nbResults);

        if ($nbResults > 0) {
            $this->object($resultList->current())
                ->isInstanceOf(
                    'Mapado\SimstringBundle\Model\SimstringResult'
                );
        }
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
