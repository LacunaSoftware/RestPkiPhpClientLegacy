<?php

namespace Lacuna\RestPki\Legacy;


class PdfMarkQRCodeElement extends PdfMarkElement
{
    public $qrCodeData;
    public $drawQuietZones;

    public function __construct($relativeContainer = null, $qrCodeData = null)
    {
        parent::__construct(PdfMarkElementType::QRCODE, $relativeContainer);
        $this->qrCodeData = $qrCodeData;
    }
}