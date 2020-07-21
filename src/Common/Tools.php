<?php

namespace NFSeSIMPLISS\Common;

/**
 * Auxiar Tools Class for comunications with NFSe webserver in Nacional Standard
 *
 * @category  NFePHP
 * @package   NFSeSIMPLISS
 * @copyright NFePHP Copyright (c) 2008-2018
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-bhiss for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;
use NFSeSIMPLISS\RpsInterface;
use NFSeSIMPLISS\Common\Signer;
use NFSeSIMPLISS\Common\Soap\SoapInterface;
use NFSeSIMPLISS\Common\Soap\SoapCurl;

class Tools
{
    public $lastRequest;
    
    protected $config;
    protected $prestador;
    protected $certificate;
    protected $wsobj;
    protected $soap;
    protected $environment;
    
    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        $this->config = json_decode($config);
        $this->certificate = $cert;
        $this->buildPrestadorTag();
        $this->wsobj = $this->loadWsobj($this->config->cmun);
        $this->environment = 'homologacao';
        if ($this->config->tpamb === 1) {
            $this->environment = 'producao';
        }
    }
    
    /**
     * load webservice parameters
     * @param string $cmun
     * @return object
     * @throws \Exception
     */
    protected function loadWsobj($cmun)
    {
        $path = realpath(__DIR__ . "/../../storage/urls_webservices.json");
        $urls = json_decode(file_get_contents($path), true);
        if (empty($urls[$cmun])) {
            throw new \Exception("Não localizado parâmetros para esse municipio.");
        }
        return (object) $urls[$cmun];
    }


    /**
     * SOAP communication dependency injection
     * @param SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
    }
    
    /**
     * Build tag Prestador
     */
    protected function buildPrestadorTag()
    {
        $this->prestador = "<Prestador>"
            . "<Cnpj>" . $this->config->cnpj . "</Cnpj>"
            . "<InscricaoMunicipal>" . $this->config->im . "</InscricaoMunicipal>"
            . "</Prestador>";
    }

    /**
     * Sign XML passing in content
     * @param string $content
     * @param string $tagname
     * @param string $mark
     * @return string XML signed
     */
    public function sign($content, $tagname, $mark)
    {
        $xml = Signer::sign(
            $this->certificate,
            $content,
            $tagname,
            $mark
        );
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        return $dom->saveXML($dom->documentElement);
    }
    
    /**
     * Send message to webservice
     * @param string $message
     * @param string $operation
     * @return string XML response from webservice
     */
    public function send($message, $operation)
    {
        $action = "{$this->wsobj->soapns}/$operation";
        $url = $this->wsobj->homologacao;
        if ($this->environment === 'producao') {
            $url = $this->wsobj->producao;
        }
        if (empty($url)) {
            throw new \Exception("Não está registrada a URL para o ambiente "
                . "de {$this->environment} desse municipio.");
        }
        
        $message = $this->putpParamInNfs($message);
        //print_r($message);////LOGGG
        $request = $this->createSoapRequest($message, $operation);
        $this->lastRequest = $request;
        
        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $msgSize = strlen($request);
        $parameters = [
            "Content-Type: text/xml;charset=UTF-8",
            "SOAPAction: \"$action\"",
            "Content-length: $msgSize"
        ];
        $response = (string) $this->soap->send(
            $operation,
            $url,
            $action,
            $request,
            $parameters
        );
        return $this->extractContentFromResponse($response, $operation);
    }
    
    /**
     * Extract xml response from CDATA outputXML tag
     * @param string $response Return from webservice
     * @return string XML extracted from response
     */
    protected function extractContentFromResponse($response, $operation)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if (!empty($dom->getElementsByTagName('outputXML')->item(0))) {
            $node = $dom->getElementsByTagName('outputXML')->item(0);
            return $node->textContent;
        }
        return $response;
    }

    /**
     * Build SOAP request
     * @param string $message
     * @param string $operation
     * @return string XML SOAP request
     */
    protected function createSoapRequest($message, $operation)
    {
        $env = "<soapenv:Envelope "
            . "xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">"
            . "<soapenv:Header/>"
            . "<soapenv:Body>"
            . $message
            . "</soapenv:Body>"
            . "</soapenv:Envelope>";
            
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($env);

        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Create tag Prestador and insert into RPS xml
     * @param RpsInterface $rps
     * @return string RPS XML (not signed)
     */
    protected function putPrestadorInRps(RpsInterface $rps)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($rps->render());
        $referenceNode = $dom->getElementsByTagName('Servico')->item(0);
        $node = $dom->createElement('Prestador');
        $dom->addChild(
            $node,
            "Cnpj",
            $this->config->cnpj,
            true
        );
        $dom->addChild(
            $node,
            "InscricaoMunicipal",
            $this->config->im,
            true
        );
        $dom->insertAfter($node, $referenceNode);
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Create tag Prestador and insert into NFS xml
     * @param RpsInterface $rps
     * @return string NFS XML (not signed)
     */
    protected function putPrestadorInNfs(RpsInterface $rps)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($rps->render());
        $referenceNode = $dom->getElementsByTagName('GerarNovaNfseEnvio')->item(0);
        $node = $dom->createElement('Prestador');
        $dom->addChild(
            $node,
            "Cnpj",
            $this->config->cnpj,
            true
        );
        $dom->addChild(
            $node,
            "InscricaoMunicipal",
            $this->config->im,
            true
        );
        $dom->appendChild($node);
        $referenceNode->insertBefore($node,$referenceNode->firstChild);
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Create tag pParam and insert into XML message soap
     * @param string $message
     * @return string XML message soap with pParam
     */
    protected function putpParamInNfs($response)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if($dom->getElementsByTagName('GerarNovaNfseEnvio')->item(0) !== null)
        {
            $referenceNode = $dom->getElementsByTagName('GerarNovaNfseEnvio')->item(0);
        }
        if($dom->getElementsByTagName('CancelarNfseEnvio')->item(0) !== null)
        {
            $referenceNode = $dom->getElementsByTagName('CancelarNfseEnvio')->item(0);
        }
        $node = $dom->createElement('pParam');
        $dom->addChild(
            $node,
            "P1",
            $this->config->cnpj,
            true
        );
        $dom->addChild(
            $node,
            "P2",
            $this->config->passws,
            true
        );
        $dom->insertAfter($node, $referenceNode);
        $p1 = $dom->getElementsByTagName('P1')->item(0);
        $p2 = $dom->getElementsByTagName('P2')->item(0);
        $p1->setAttribute('xmlns', 'http://www.sistema.com.br/Sistema.Ws.Nfse.Cn');
        $p2->setAttribute('xmlns', 'http://www.sistema.com.br/Sistema.Ws.Nfse.Cn');
        if($dom->getElementsByTagName('Prestador')->item(0) !== null)
        {
            $pres = $dom->getElementsByTagName('Prestador')->item(0);
            $pres->setAttribute('xmlns', 'http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd');
        }
        if($dom->getElementsByTagName('InformacaoNfse')->item(0) !== null)
        {
            $Infnf = $dom->getElementsByTagName('InformacaoNfse')->item(0);
            $Infnf->setAttribute('xmlns', 'http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd');
        }
        if($dom->getElementsByTagName('Pedido')->item(0) !== null)
        {
            $Infnf = $dom->getElementsByTagName('Pedido')->item(0);
            $Infnf->setAttribute('xmlns', 'http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd');
        }
        if($dom->getElementsByTagName('InfPedidoCancelamento')->item(0) !== null)
        {
            $Infnf = $dom->getElementsByTagName('InfPedidoCancelamento')->item(0);
            $Infnf->setAttribute('xmlns', 'http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd');
        }

        return $dom->saveXML($dom->documentElement);
    }
}
