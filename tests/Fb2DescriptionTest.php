<?php

namespace Litlife\Fb2\Tests;

use DOMElement;
use Litlife\Fb2\Fb2;
use PHPUnit\Framework\TestCase;

class Fb2DescriptionTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test()
    {
        $fb2 = new Fb2();
        $fb2->setFile(__DIR__ . '/books/test.fb2');
        $fb2->loadDescription();

        $this->assertInstanceOf(DOMElement::class, $fb2->description()->getNode());
    }
}
