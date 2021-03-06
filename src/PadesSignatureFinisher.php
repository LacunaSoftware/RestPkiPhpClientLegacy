<?php

namespace Lacuna\RestPki\Legacy;

class PadesSignatureFinisher extends SignatureFinisher
{
    private $signedPdf;

    public function __construct($restPkiClient)
    {
        parent::__construct($restPkiClient);
    }

    public function finish()
    {
        $request = null;

        if (empty($this->token)) {
            throw new \Exception("The token was not set");
        }

        if (empty($this->signature)) {
            $response = $this->restPkiClient->post("Api/PadesSignatures/{$this->token}/Finalize", null);
        } else {
            $request['signature'] = $this->signature;
            $response = $this->restPkiClient->post("Api/PadesSignatures/{$this->token}/Finalize", $request);
        }

        $this->signedPdf = base64_decode($response->signedPdf);
        $this->callbackArgument = $response->callbackArgument;
        $this->certificateInfo = $response->certificate;
        $this->done = true;

        return $this->signedPdf;
    }

    public function getSignedPdf()
    {
        if (!$this->done) {
            throw new \Exception('The getSignedPdf() method can only be called after calling one of the Finish methods');
        }

        return $this->signedPdf;
    }

    public function writeSignedPdfToPath($pdfPath)
    {
        if (!$this->done) {
            throw new \Exception('The method writeSignedPdfToPath() can only be called after calling the finish() method');
        }

        file_put_contents($pdfPath, $this->signedPdf);
    }
}