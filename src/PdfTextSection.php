<?php

namespace Lacuna\RestPki\Legacy;

class PdfTextSection
{
    public $style;
    public $text;
    public $color;
    public $fontSize;

    public function __construct($text = null, $color = null, $fontSize = null)
    {
        $this->style = PdfTextStyle::NORMAL;
        $this->text = $text;
        $this->fontSize = $fontSize;
        if (empty($color)) {
            $this->color = new Color('#000000'); // Black
        } else {
            $this->color = $color;
        }
    }
}