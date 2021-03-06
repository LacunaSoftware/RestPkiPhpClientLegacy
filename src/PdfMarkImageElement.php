<?php

namespace Lacuna\RestPki\Legacy;

class PdfMarkImageElement extends PdfMarkElement
{
    public $image;

    public function __construct($relativeContainer = null, $image = null)
    {
        parent::__construct(PdfMarkElementType::IMAGE, $relativeContainer);
        $this->image = $image;
    }
}