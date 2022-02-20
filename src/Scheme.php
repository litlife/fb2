<?php

namespace Litlife\Fb2;

use DOMDocument;
use DOMElement;
use DOMXpath;

class Scheme
{
    private string $schemePath;
    private DOMDocument $dom;
    private DOMXpath $xpath;

    public function __construct()
    {
        $this->schemePath = __DIR__ . '/../xsd/FictionBook2.2.xsd';
    }

    public function loadScheme()
    {
        $this->dom = new DOMDocument();
        $this->dom->load($this->schemePath);

        $this->xpath = new DOMXpath($this->dom);
    }

    public function dom(): DOMDocument
    {
        return $this->dom;
    }

    public function getRule(string $name): DOMElement
    {
        return $this->xpath
            ->query('//*[local-name()=\'element\'][@name=\'' . $name . '\']')
            ->item(0);
    }

    public function getFictionBookRule(): DOMElement
    {
        return $this->xpath
            ->query('//*[local-name()=\'element\'][@name=\'FictionBook\']')
            ->item(0);
    }

    public function getDescriptionRule(): DOMElement
    {
        return $this->xpath
            ->query('//*[local-name()=\'element\'][@name=\'FictionBook\']//*[local-name()=\'element\'][@name=\'description\']')
            ->item(0);
    }
}
