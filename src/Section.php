<?php

namespace Litlife\Fb2;

class Section extends Tag
{
    public function getTitle(): string
    {
        $nodeList = $this->getFb2()->xpath->query("./n:title", $this->getNode());

        $title = '';

        if ($nodeList->length) {

            foreach ($nodeList as $node) {
                $title .= $node->nodeValue . ' ';
            }
        }

        $title = trim($title);

        if ($title == '') {

            $childs = $this->getFb2()->xpath->query("./*", $this->getNode());

            if ($childs->length) {

                foreach ($childs as $child) {
                    if (trim($child->nodeValue) != "") {
                        $title = trim($child->nodeValue);

                        if (mb_strlen($title) > 100) {
                            $title = mb_substr($title, 0, 96) . ' ...';
                        }

                        break;
                    }
                }
            }
        }

        $title = preg_replace("/[[:space:]]+/iu", " ", $title);

        return trim($title);
    }

    public function getNodeValue(): string
    {
        return $this->getNode()->nodeValue;
    }

    public function getSections(): array
    {
        $sections = [];

        foreach ($this->getFb2()->xpath->query("./n:section", $this->getNode()) as $section) {
            $sections[] = new Section($this->getFb2(), $section);
        }

        return $sections;
    }

    public function getFb2Id(): ?string
    {
        if ($this->getNode()->hasAttribute('id'))
            return $this->getNode()->getAttribute('id');
        else
            return null;
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

    public function isHaveInnerSections(): bool
    {
        return (bool)$this->getSectionsCount();
    }

    public function getSectionsCount(): int
    {
        return $this->getFb2()->xpath->query("*[name()='section']", $this->getNode())->length;
    }
}
