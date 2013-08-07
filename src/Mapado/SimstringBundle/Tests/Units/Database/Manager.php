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
class Manager extends atoum
{
    public function testSet()
    {
        $city = $this->getCityClient();
        $country = $this->getCountryClient();

        $manager = new Database\Manager();
        $manager->setDatabase('city', $city);
        $manager->setDatabase('country', $country);

        $this->object($manager->getDatabase('city'))
            ->isIdenticalTo($city);

        $this->object($manager->getDatabase('country'))
            ->isIdenticalTo($country);
    }

    /**
     * getCityClient
     *
     * @access private
     * @return void
     */
    private function getCityClient()
    {
        $database = __DIR__ . '/../../Database/city.db';
        $config = [
            'measure' => 'cosine',
            'threshold' => 0.7,
        ];
        $client = new Database\SimstringClient($database, $config);
        return $client;
    }

    /**
     * getCountryClient
     *
     * @access private
     * @return void
     */
    private function getCountryClient()
    {
        $database = __DIR__ . '/../../Database/country.db';
        $config = [
            'measure' => 'cosine',
            'threshold' => 0.7,
        ];
        $client = new Database\SimstringClient($database, $config);
        return $client;
    }
}
