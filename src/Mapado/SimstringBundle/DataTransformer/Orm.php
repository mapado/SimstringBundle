<?php

namespace Mapado\SimstringBundle\DataTransformer;

use Mapado\SimstringBundle\Model\SimstringResult;

class Orm implements TransformerInterface
{
    /**
     * persistenceService
     *
     * @var mixed
     * @access private
     */
    private $persistenceService;

    /**
     * model
     *
     * @var string
     * @access private
     */
    private $model;

    /**
     * field
     *
     * @var string
     * @access private
     */
    private $field;

    /**
     * options
     *
     * @var array
     * @access private
     */
    private $options;

    /**
     * __construct
     *
     * @See Interface
     */
    public function __construct($persistenceService, $model, $field, array $options = [])
    {
        $this->persistenceService = $persistenceService;
        $this->model = $model;
        $this->field = $field;
        $this->options = $options;
    }

    /**
     * find
     *
     * @See Interface
     */
    public function reverseTransform(\Iterator $stringList)
    {
        $objectList = [];

        $repo = $this->getRepository();
        $method = $this->getMethod();

        foreach ($stringList as $search) {
            $searchText = $search->getValue();

            $l = call_user_func([$repo, $method], [$this->field => $searchText]);

            foreach ($l as $result) {
                $objectList[] = new SimstringResult($result, $search->getThreshold());
            }
        }

        return $objectList;
    }

    /**
     * transform
     *
     * @param \Iterator $objectList
     * @access public
     * @return void
     */
    public function transform(\Iterator $objectList)
    {
        $stringList = [];

        foreach ($objectList as $object) {
            $getMethod = 'get' . ucfirst($this->field);
            $isMethod = 'is' . ucfirst($this->field);

            if (method_exists($object, $getMethod)) {
                $value = $object->{$getMethod}();
            } elseif (method_exists($object, $isMethod)) {
                $value = $object->{$isMethod}();
            } else {
                $value = $object->{$this->field};
            }

            $stringList[] = $value;
        }

        return $stringList;
    }

    /**
     * findAll
     *
     * @access public
     * @return void
     */
    public function findAll()
    {
        $repo = $this->getRepository();
        $method = $this->getMethod();

        return call_user_func([$repo, $method], []);
    }

    /**
     * getRepository
     *
     * @access private
     * @return EntityRepository
     */
    private function getRepository()
    {
        $manager = (isset($this->options['manager']) ? $this->options['manager'] : null);
        $entityManager = $this->persistenceService->getManager($manager);
        $repo = $entityManager->getRepository($this->model);

        return $repo;
    }

    /**
     * getMethod
     *
     * @access private
     * @return string
     */
    private function getMethod()
    {
        return (isset($this->options['repository_method']) ? $this->options['repository_method'] : 'findBy');
    }
}
