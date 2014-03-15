<?php


namespace PHPPdf\Test\Helper;

class ContainerBuilder
{
    private $attributes = array();
    private $parentBuilder;
    private $childBuilders = array();
    private $parentContainer;

    /**
     * @return ContainerBuilder
     */
    public static function create()
    {
        return new self();
    }

    private function __construct(ContainerBuilder $parentBuilder = null)
    {
        $this->parentBuilder = $parentBuilder;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return ContainerBuilder
     */
    public function attr($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param array $attrs
     *
     * @return ContainerBuilder
     */
    public function attrs(array $attrs)
    {
        $this->attributes = $attrs + $this->attributes;
        return $this;
    }

    /**
     * @return ContainerBuilder
     */
    public function parent()
    {
        $this->parentContainer = new self($this);

        return $this->parentContainer;
    }

    /**
     * @return ContainerBuilder
     */
    public function end()
    {
        return $this->parentBuilder;
    }

    /**
     * @return ContainerBuilder
     */
    public function child()
    {
        $builder = new self($this);
        $this->childBuilders[] = $builder;

        return $builder;
    }

    public function getContainer()
    {
        $container = new Container($this->attributes);

        if($this->parentContainer)
        {
            $this->parentContainer->getContainer()->add($container);
        }

        foreach($this->childBuilders as $childBuilder)
        {
            $container->add($childBuilder->getContainer());
        }

        return $container;
    }
}