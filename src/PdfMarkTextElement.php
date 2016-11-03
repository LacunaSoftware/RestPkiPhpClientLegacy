<?php

namespace Lacuna\RestPki\ClientLegacy;

class PdfMarkTextElement extends PdfMarkElement
{
    public $textSections;

    public function __construct()
    {
        $args = func_get_args();
        if (sizeof($args) == 0) { // Case ()
            $this->textSections = array();
            parent::__construct(PdfMarkElementType::TEXT);
        } elseif (sizeof($args) == 2) {
            $this->textSections = $args[1];
            parent::__construct(PdfMarkElementType::TEXT, $args[0]);
        } else {
            throw new \InvalidArgumentException("Invalid parameters passed to the PdfMarkTextElement's Constructor.");
        }
    }
}