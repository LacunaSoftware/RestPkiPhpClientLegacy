<?php

namespace Lacuna\RestPki\Legacy;

class XmlElementSignatureStarter extends XmlSignatureStarter
{

    private $toSignElementId;
    /** @var XmlIdResolutionTable */
    private $idResolutionTable;

    public function __construct($restPkiClient)
    {
        parent::__construct($restPkiClient);
    }

    public function setToSignElementId($toSignElementId)
    {
        $this->toSignElementId = $toSignElementId;
    }

    public function setIdResolutionTable(XmlIdResolutionTable $idResolutionTable)
    {
        $this->idResolutionTable = $idResolutionTable;
    }

    public function startWithWebPki()
    {
        parent::verifyCommonParameters(true);

        if (empty($this->xmlContent)) {
            throw new \Exception('The XML was not set');
        }
        if (empty($this->toSignElementId)) {
            throw new \Exception('The XML element Id to sign was not set');
        }

        $request = parent::getRequest();
        $request['elementToSignId'] = $this->toSignElementId;
        if ($this->idResolutionTable != null) {
            $request['idResolutionTable'] = $this->idResolutionTable->toModel();
        }

        $response = $this->restPkiClient->post('Api/XmlSignatures/XmlElementSignature', $request);
        return $response->token;
    }
}