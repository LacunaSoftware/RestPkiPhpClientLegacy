<?php

namespace Lacuna\RestPki\Legacy;

class PadesSignatureExplorer extends SignatureExplorer
{
    const PDF_MIME_TYPE = "application/pdf";

    public function __construct($client)
    {
        parent::__construct($client);
    }

    public function open()
    {
        if (!isset($this->signatureFileContent)) {
            throw new \RuntimeException("The signature file to open not set");
        } else {
            $request = $this->getRequest($this::PDF_MIME_TYPE);
            $response = $this->restPkiClient->post("Api/PadesSignatures/Open", $request);

            foreach ($response->signers as $signer) {
                $signer->validationResults = new ValidationResults($signer->validationResults);
                $signer->messageDigest->algorithm = DigestAlgorithm::getInstanceByApiAlgorithm(
                    $signer->messageDigest->algorithm
                );
                if (isset($signer->certificate)) {
                    if (isset($signer->certificate->pkiBrazil)) {

                        if (isset($signer->certificate->pkiBrazil->cpf)) {
                            $cpf = $signer->certificate->pkiBrazil->cpf;
                            $signer->certificate->pkiBrazil->cpfFormatted = substr($cpf, 0, 3) . '.' . substr($cpf, 3,
                                    3)
                                . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
                        } else {
                            $signer->certificate->pkiBrazil->cpfFormatted = '';
                        }

                        if (isset($signer->certificate->pkiBrazil->cnpj)) {
                            $cnpj = $signer->certificate->pkiBrazil->cnpj;
                            $signer->certificate->pkiBrazil->cnpjFormatted = substr($cnpj, 0, 2) . '.' . substr($cnpj,
                                    2, 3)
                                . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12);
                        } else {
                            $signer->certificate->pkiBrazil->cnpjFormatted = '';
                        }
                    }
                }
            }

            return $response;
        }
    }
}