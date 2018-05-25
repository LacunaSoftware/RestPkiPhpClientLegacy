<?php

namespace Lacuna\RestPki\ClientLegacy;


class PdfMarker
{
    /** @var RestPkiClient */
    private $client;

    public $measurementUnits;
    public $pageOptimization;
    public $abortIfSigned;
    public $marks;
    public $fileContent;

    public function __construct($client)
    {
        $this->client = $client;
        $this->marks = array();
        $this->measurementUnits = PadesMeasurementUnits::CENTIMETERS;
    }

    public function setFileFromPath($path)
    {
        $this->fileContent = file_get_contents($path);
    }

    public function setFileFromContentRaw($contentRaw)
    {
        $this->fileContent = $contentRaw;
    }

    public function setFileFromContentBase64($contentBase64)
    {
        $this->fileContent = base64_decode($contentBase64);
    }

    public function apply()
    {
        $request = array(
            'marks' => $this->marks,
            'measurementUnits' => $this->measurementUnits,
            'pageOptimization' => $this->pageOptimization,
            'abortIfSigned' => $this->abortIfSigned
        );
        $request['file'] = array(
            'content' => base64_encode($this->fileContent)
        );
        $response = $this->client->post('Api/Pdf/AddMarks', $request);
        return $response->file;
    }
}