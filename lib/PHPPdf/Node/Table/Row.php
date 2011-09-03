<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Node\Table;

use PHPPdf\Node\Table\Cell,
    PHPPdf\Node\Container,
    PHPPdf\Node\Listener,
    PHPPdf\Node\Node;

/**
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class Row extends Container implements Listener
{
    private $numberOfColumns = 0;
    private $maxHeightOfCells = 0;
    private $marginsOfCells = array(
        'margin-top' => 0,
        'margin-bottom' => 0,
    );
    
    public function initialize()
    {
        parent::initialize();
        
        $this->setAttribute('splittable', false);
    }

    public function add(Node $node)
    {
        if(!$node instanceof Cell)
        {
            throw new \InvalidArgumentException(sprintf('Invalid child node type, expected PHPPdf\Node\Table\Cell, %s given.', get_class($node)));
        }

        $node->setNumberOfColumn($this->numberOfColumns);
        $this->numberOfColumns += $node->getColspan();
        $parent = $this->getParent();

        if($parent)
        {
            $node->addListener($parent);
        }

        $node->addListener($this);

        $this->setMaxHeightOfCellsIfHeightOfPassedCellIsGreater($node);
        $this->setCellMarginIfNecessary($node, 'margin-top');
        $this->setCellMarginIfNecessary($node, 'margin-bottom');

        return parent::add($node);
    }

    private function setMaxHeightOfCellsIfHeightOfPassedCellIsGreater(Cell $node)
    {
        $height = $node->getHeight();

        if($height > $this->maxHeightOfCells)
        {
            $this->maxHeightOfCells = $height;
        }
    }

    private function setCellMarginIfNecessary(Cell $cell, $marginType)
    {
        $margin = $cell->getAttribute($marginType);

        if($margin > $this->marginsOfCells[$marginType])
        {
            $this->marginsOfCells[$marginType] = $margin;
        }
    }

    public function getMarginsTopOfCells()
    {
        return $this->marginsOfCells['margin-top'];
    }

    public function getMarginsBottomOfCells()
    {
        return $this->marginsOfCells['margin-bottom'];
    }

    public function getHeight()
    {
        $height = parent::getHeight();

        if($height === null)
        {
            $height = $this->getAncestorByType('PHPPdf\Node\Table')->getRowHeight();
        }

        return $height;
    }

    public function getWidth()
    {
        return $this->getParent()->getWidth();
    }

    public function reset()
    {
        parent::reset();
        $this->numberOfColumns = 0;
    }

    public function attributeChanged(Node $node, $attributeName, $oldValue)
    {
        if($attributeName === 'height')
        {
            $this->setMaxHeightOfCellsIfHeightOfPassedCellIsGreater($node);
        }
    }

    public function parentBind(Node $node)
    {
        $this->setMaxHeightOfCellsIfHeightOfPassedCellIsGreater($node);
    }

    public function getMaxHeightOfCells()
    {
        return $this->maxHeightOfCells;
    }
}