<?php

namespace Mapado\SimstringBundle\DataTransformer;

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
        $manager = (isset($this->options['manager']) ? $this->options['manager'] : null);
        $method = (isset($this->options['repository_method']) ? $this->options['repository_method'] : 'findBy');


        $em = $this->persistenceService->getManager($manager);
        $repo = $em->getRepository($this->model);

        foreach ($stringList as $search) {
            $l = call_user_func([$repo, $method], [$this->field => $search]);
            $objectList = array_merge($objectList, $l);
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
                $value = $this->{$this->field};
            }

            $stringList[] = $value;
        }

        return $stringList;
    }
}
