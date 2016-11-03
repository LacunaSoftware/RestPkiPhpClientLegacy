<?php

namespace Lacuna\RestPki\ClientLegacy;

class PdfMarkImageElement extends PdfMarkElement
{
    public $image;

    public function __construct()
    {
        $args = func_get_args();
        if (sizeof($args) == 0) { // Case ()
            parent::__construct(PdfMarkElementType::IMAGE);
        } elseif (sizeof($args) == 2) { // Case (relativeContainer, image)
            $this->image = $args[1];
            parent::__construct(PdfMarkElementType::IMAGE, $args[0]);
        } else {
            throw new \InvalidArgumentException("Invalid parameters to the PdfMarkImageElement's Constructor.");
        }
    }
}