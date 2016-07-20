<?php
/**
 * Created by PhpStorm.
 * User: maigoxin 
 * Date: 5/17/16
 * Time: 3:54 AM
 */
namespace EasyLib;


use Interop\Container\ContainerInterface;

/**
 * 解决依赖注入问题，从Controller中可以直接访问所有Service
 * 
 * Class BaseController
 */
class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $_container;
    
    public function __construct(ContainerInterface &$container)
    {
        $this->_container = $container;
    }

    final public function setContainer(ContainerInterface &$container)
    {
        $this->_container = $container;
    }
    
    final public function getContainer()
    {
        return $this->_container;
    }
    
    final public function __get($name)
    {
        return $this->_container->get($name);
    }
    
    final public function __isset($name)
    {
        return $this->_container->has($name);
    }
}
