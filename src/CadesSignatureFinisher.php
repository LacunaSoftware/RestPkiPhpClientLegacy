<?php

namespace Lacuna\RestPki\Legacy;

class CadesSignatureFinisher extends SignatureFinisher
{
    private $cms;

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
            $response = $this->restPkiClient->post("Api/CadesSignatures/{$this->token}/Finalize", null);
        } else {
            $request['signature'] = $this->signature;
            $response = $this->restPkiClient->post("Api/CadesSignatures/{$this->token}/Finalize", $request);
        }

        $this->cms = base64_decode($response->cms);
        $this->callbackArgument = $response->callbackArgument;
        $this->certificateInfo = $response->certificate;
        $this->done = true;

        return $this->cms;
    }

    public function getCms()
    {
        if (!$this->done) {
            throw new \InvalidArgumentException("The getCms() method can only be called after calling one of the Finish method");
        }

        return $this->cms;
    }

    public function writeCmsfToPath($path)
    {
        if (!$this->done) {
            throw new \Exception('The method writeCmsfToPath() can only be called after calling the finish() method');
        }

        file_put_contents($path, $this->cms);
    }
}