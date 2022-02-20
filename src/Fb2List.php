<?php

namespace Litlife\Fb2;

use DOMNodeList;
use Iterator;

class Fb2List implements Iterator
{
    private DOMNodeList $nodeList;
    private Fb2 $fb2;
    private int $position = 0;

    public function __construct(Fb2 &$fb2, DOMNodeList &$nodeList)
    {
        $this->fb2 = &$fb2;
        $this->nodeList = &$nodeList;
    }

    public function count(): int
    {
        return $this->nodeList->length;
    }

    public function first(): Section|Description|Tag|null
    {
        return $this->item(0);
    }

    public function item(int $index): Section|Description|Tag|null
    {
        $node = $this->nodeList->item($index);

        if (empty($node))
            return null;
        else {
            if ($node->nodeName == 'section') {
                return new Section($this->fb2, $node);
            } elseif ($node->nodeName == 'description') {
                return new Description($this->fb2, $node);
            } else {
                return new Tag($this->fb2, $node);
            }
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Section|Description|Tag
    {
        $node = $this->nodeList->item($this->position);

        if ($node->nodeName == 'section') {
            return new Section($this->fb2, $node);
        } elseif ($node->nodeName == 'description') {
            return new Description($this->fb2, $node);
        } else {
            return new Tag($this->fb2, $node);
        }
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        $item = $this->nodeList->item($this->position);
        return isset($item);
    }

}
