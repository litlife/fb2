<?php

namespace Litlife\Fb2;

use DOMElement;
use DOMNode;

class Tag
{
    protected array $scheme = [];
    private mixed $node;
    private Fb2 $fb2;
    private array $nodes = [];

    function __construct(Fb2 $fb2, string|DOMElement|null $node = null)
    {
        $this->fb2 = $fb2;

        if (is_string($node)) {
            $this->node = $fb2->dom()->createElementNS($this->fb2->getNameSpace(), $node);
        } else {
            $this->node = $node;
        }

        $this->load();
    }

    public function load()
    {
        $this->nodes = [];

        foreach ($this->scheme as $name => $rule) {
            foreach ($this->fb2->xpath()->query('*[local-name()=\'' . $name . '\']', $this->node) as $node) {
                $this->nodes[$name][] = new Tag($this->fb2, $node);
            }
        }
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function setAttribute(string $name, string $value)
    {
        $this->getNode()->setAttributeNS($this->fb2->getNameSpace(), $name, $value);
    }

    public function getNode(): DOMElement
    {
        return $this->node;
    }

    public function getXML(): string
    {
        return $this->fb2->dom()->saveXML($this->getNode(), LIBXML_NOEMPTYTAG);
    }

    public function appendChild(Tag $tag): DOMNode
    {
        return $this->getNode()->appendChild($tag->getNode());
    }

    public function create(string $name, string $value = null): Tag
    {
        $name = str_replace('_', '-', $name);

        $tag = new Tag($this->fb2, $name);

        if (!empty($value)) {

            if (is_array($value))
                $value = current($value);

            $tag->setValue($value);
        }

        $this->getNode()->appendChild($tag->getNode());

        return $tag;
    }

    public function setValue(string $value)
    {
        foreach ($this->getNode()->childNodes as $node) {
            $this->getNode()->removeChild($node);
        }

        $text = $this->fb2->dom()->createTextNode($value);

        $this->getNode()->appendChild($text);
    }

    public function delete()
    {
        $this->getNode()->parentNode->removeChild($this->getNode());
    }

    public function getParent(): DOMNode
    {
        return $this->getNode()->parentNode;
    }

    public function hasChild(string $name): bool
    {
        return (boolean)$this->query("*[local-name()='" . $name . "']")->first();
    }

    public function query(string $query): Fb2List
    {
        $nodeList = $this->fb2->xpath->query($query, $this->getNode());

        return new Fb2List($this->fb2, $nodeList);
    }

    public function getFirstChildValue(string $name): ?string
    {
        $child = $this->getFirstChild($name);

        if (!empty($child))
            return $child->getNodeValue();
        else
            return null;
    }

    public function getFirstChild(string $name): Section|Description|Tag|null
    {
        foreach ($this->childs() as $child) {
            if ($child->getNodeName() == $name)
                return $child;
        }

        return null;
    }

    public function childs(string $name = null): Fb2List
    {
        if (empty($name)) {
            $nodeList = $this->fb2->xpath->query('child::*', $this->getNode());
        } else {
            $nodeList = $this->fb2->xpath->query("child::*[local-name()='" . $name . "']", $this->getNode());
        }
        return new Fb2List($this->fb2, $nodeList);
    }

    public function getNodeName(): string
    {
        return $this->getNode()->nodeName;
    }

    public function getNodeValue(): string
    {
        return $this->getNode()->nodeValue;
    }

    public function isHaveImages(): bool
    {
        $childs = $this->getFb2()->xpath->query("*[not(name()='title' or name()='section')]", $this->getNode());

        if ($childs->length) {

            foreach ($childs as $child) {
                //dump('child '.$child->nodeName);
                if ($child->nodeName == 'image') {
                    return true;
                } else {
                    $childDescendants = $this->getFb2()->xpath->query("*", $child);
                    foreach ($childDescendants as $descendant) {
                        //dump('descendant '.$descendant->nodeName);
                        if ($descendant->nodeName == 'image')
                            return true;
                    }
                }
            }
        }

        return false;
    }

    public function getFb2(): Fb2
    {
        return $this->fb2;
    }

    public function isHaveInnerSections(): bool
    {
        return (bool)$this->getSectionsCount();
    }

    public function getSectionsCount(): int
    {
        return $this->getFb2()->xpath->query("*[name()='section']", $this->getNode())->length;
    }
}
