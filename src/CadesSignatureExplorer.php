<?php

namespace Lacuna\RestPki\Legacy;

class CadesSignatureExplorer extends SignatureExplorer
{
    const CMS_SIGNATURE_MIME_TYPE = "application/pkcs7-signature";

    private $dataFileContent;

    public function __construct($client)
    {
        parent::__construct($client);
    }

    public function setDataFile($filePath)
    {
        $this->dataFileContent = file_get_contents($filePath);
    }

    public function open()
    {
        return $this->openCommon(false);
    }

    public function openAndExtractContent()
    {
        $signature = $this->openCommon(true);
        $encapsulatedContent = new FileResult($this->restPkiClient, $signature->encapsulatedContent);
        return new CadesSignatureWithEncapsulatedContent($signature, $encapsulatedContent);
    }

    public function openCommon($extractEncapsulatedContent)
    {

        if (!isset($this->signatureFileContent)) {
            throw new \RuntimeException("The signature file to open not set");
        }

        $request = $this->getRequest(self::CMS_SIGNATURE_MIME_TYPE);
        $request['extractEncapsulatedContent'] = $extractEncapsulatedContent;

        if ($this->dataFileContent != null) {
            $requiredHashes = $this->getRequiredHashes();
            if (count($requiredHashes) > 0) {
                $request['dataHashes'] = $this->computeDataHashes($this->dataFileContent, $requiredHashes);
            }
        }

        $response = $this->restPkiClient->post("Api/CadesSignatures/Open", $request);

        foreach ($response->signers as $signer) {
            $signer->validationResults = new ValidationResults($signer->validationResults);
            $signer->messageDigest->algorithm = DigestAlgorithm::getInstanceByApiAlgorithm($signer->messageDigest->algorithm);

            if (isset($signer->certificate)) {
                if (isset($signer->certificate->pkiBrazil)) {

                    if (isset($signer->certificate->pkiBrazil->cpf)) {
                        $cpf = $signer->certificate->pkiBrazil->cpf;
                        $signer->certificate->pkiBrazil->cpfFormatted =
                            sprintf('%s.%s.%s-%s',
                                substr($cpf, 0, 3),
                                substr($cpf, 3, 3),
                                substr($cpf, 6, 3),
                                substr($cpf, 9));
                    } else {
                        $signer->certificate->pkiBrazil->cpfFormatted = '';
                    }

                    if (isset($signer->certificate->pkiBrazil->cnpj)) {
                        $cnpj = $signer->certificate->pkiBrazil->cnpj;
                        $signer->certificate->pkiBrazil->cnpjFormatted =
                            sprintf('%s.%s.%s/%s-%s',
                                substr($cnpj, 0, 2),
                                substr($cnpj, 2, 3),
                                substr($cnpj, 5, 3),
                                substr($cnpj, 8, 4),
                                substr($cnpj, 12));
                    } else {
                        $signer->certificate->pkiBrazil->cnpjFormatted = '';
                    }
                }
            }
        }

        return $response;
    }

    private function getRequiredHashes()
    {
        $request = array(
            "content" => base64_encode($this->signatureFileContent),
            "mimeType" => self::CMS_SIGNATURE_MIME_TYPE
        );

        $response = $this->restPkiClient->post("Api/CadesSignatures/RequiredHashes", $request);

        $algs = array();

        foreach ($response as $alg) {
            array_push($algs, DigestAlgorithm::getInstanceByApiAlgorithm($alg));
        }

        return $algs;
    }

    private function computeDataHashes($dataFileStream, $algorithms)
    {
        $dataHashes = array();

        foreach ($algorithms as $algorithm) {
            $digestValue = mhash($algorithm->getHashId(), $dataFileStream);
            $dataHash = array(
                'algorithm' => $algorithm->getAlgorithm(),
                'value' => base64_encode($digestValue)
            );
            array_push($dataHashes, $dataHash);
        }

        return $dataHashes;
    }
}