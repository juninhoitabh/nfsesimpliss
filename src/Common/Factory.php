<?php

namespace NFSeSIMPLISS\Common;

/**
 * Class for RPS/NFS XML convertion
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

use stdClass;
use NFePHP\Common\DOMImproved as Dom;
use DOMNode;
use DOMElement;

class Factory
{
    /**
     * @var stdClass
     */
    protected $std;
    /**
     * @var Dom
     */
    protected $dom;
    /**
     * @var DOMNode
     */
    protected $rps;
    /**
     * @var DOMNode
     */
    protected $nfs;
    /**
     * @var \stdClass
     */
    protected $config;
    /** tipo de geração 1 RPS 2 NFS
     * @var string
     */
    protected $tp;

    /**
     * Constructor
     * @param stdClass $std
     */
    public function __construct(stdClass $std, $tp = 1)
    {
        $this->std = $std;

        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
        if($tp == 1)
        {
            $this->tp  = $tp;
            $this->rps = $this->dom->createElementNS('http://www.abrasf.org.br/nfse.xsd', 'Rps');
        }
        else
        {
            $this->tp  = $tp;
            $this->nfs = $this->dom->createElementNS('http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd', 'GerarNovaNfseEnvio');
        }
    }

    /**
     * Add config
     * @param \stdClass $config
     */
    public function addConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Builder, converts sdtClass Rps in XML Rps
     * NOTE: without Prestador Tag
     * @return string RPS in XML string format
     */
    public function render()
    {
        if($this->tp == 1)
        {
            $num = '';
            if (!empty($this->std->identificacaorps->numero)) {
                $num = $this->std->identificacaorps->numero;
            }
            $infRps = $this->dom->createElement('InfRps');
            $att = $this->dom->createAttribute('Id');
            $att->value = "rps{$num}";
            $infRps->appendChild($att);

            $this->addIdentificacao($infRps);

            $this->dom->addChild(
                $infRps,
                "DataEmissao",
                $this->std->dataemissao,
                true
            );
            $this->dom->addChild(
                $infRps,
                "NaturezaOperacao",
                $this->std->naturezaoperacao,
                true
            );
            $this->dom->addChild(
                $infRps,
                "RegimeEspecialTributacao",
                !empty($this->std->regimeespecialtributacao)
                    ? $this->std->regimeespecialtributacao
                    : null,
                false
            );
            $this->dom->addChild(
                $infRps,
                "OptanteSimplesNacional",
                $this->std->optantesimplesnacional,
                true
            );
            $this->dom->addChild(
                $infRps,
                "IncentivadorCultural",
                $this->std->incentivadorcultural,
                false
            );
            $this->dom->addChild(
                $infRps,
                "Status",
                $this->std->status,
                true
            );

            $this->addServico($infRps);
            $this->addPrestador($infRps);
            $this->addTomador($infRps);
            $this->addIntermediario($infRps);
            $this->addConstrucao($infRps);

            $this->rps->appendChild($infRps);
            $this->dom->appendChild($this->rps);
            return $this->dom->saveXML();
        }
        else
        {            
            $num = '';
            if (!empty($this->std->idnfs)) {
                $num = $this->std->idnfs;
            }
            $infNfse = $this->dom->createElement('InformacaoNfse');
            $att = $this->dom->createAttribute('id');
            $att->value = "nfs{$num}";
            $infNfse->appendChild($att);

            $this->dom->addChild(
                $infNfse,
                "NaturezaOperacao",
                $this->std->naturezaoperacao,
                true
            );
            $this->dom->addChild(
                $infNfse,
                "RegimeEspecialTributacao",
                !empty($this->std->regimeespecialtributacao)
                    ? $this->std->regimeespecialtributacao
                    : null,
                false
            );
            $this->dom->addChild(
                $infNfse,
                "OptanteSimplesNacional",
                $this->std->optantesimplesnacional,
                true
            );
            $this->dom->addChild(
                $infNfse,
                "IncentivadorCultural",
                $this->std->incentivadorcultural,
                false
            );
            $this->dom->addChild(
                $infNfse,
                "Status",
                $this->std->status,
                true
            );
            $this->dom->addChild(
                $infNfse,
                "Competencia",
                $this->std->competencia,
                true
            );
            $this->dom->addChild(
                $infNfse,
                "NfseSubstituida",
                !empty($this->std->nfsesubstituida)
                    ? $this->std->nfsesubstituida
                    : null,
                false
            );
            $this->dom->addChild(
                $infNfse,
                "OutrasInformacoes",
                !empty($this->std->outrasinformacoes)
                    ? $this->std->outrasinformacoes
                    : null,
                false
            );


            $this->addServico($infNfse);
            $this->addTomador($infNfse);
            $this->addIntermediario($infNfse);
            $this->addConstrucao($infNfse);

            $this->nfs->appendChild($infNfse);
            $this->dom->appendChild($this->nfs);
            return $this->dom->saveXML();
        }
    }

    /**
     * Includes Identificacao TAG in parent NODE
     * @param DOMNode $parent
     */
    protected function addIdentificacao(&$parent)
    {
        $id = $this->std->identificacaorps;
        $node = $this->dom->createElement('IdentificacaoRps');
        $this->dom->addChild(
            $node,
            "Numero",
            $id->numero,
            true
        );
        $this->dom->addChild(
            $node,
            "Serie",
            $id->serie,
            true
        );
        $this->dom->addChild(
            $node,
            "Tipo",
            $id->tipo,
            true
        );
        $parent->appendChild($node);
    }

    /**
     * Includes prestador
     * @param DOMNode $parent
     * @return void
     */
    protected function addPrestador(&$parent)
    {
        if (!isset($this->config)) {
            return;
        }
        $node = $this->dom->createElement('Prestador');
        $this->dom->addChild(
            $node,
            "Cnpj",
            !empty($this->config->cnpj) ? $this->config->cnpj : null,
            false
        );
        $this->dom->addChild(
            $node,
            "Cpf",
            !empty($this->config->cpf) ? $this->config->cpf : null,
            false
        );
        $this->dom->addChild(
            $node,
            "InscricaoMunicipal",
            $this->config->im,
            true
        );
        $parent->appendChild($node);
    }

    /**
     * Includes Servico TAG in parent NODE
     * @param DOMNode $parent
     */
    protected function addServico(&$parent)
    {
        $serv = $this->std->servico;
        $val = $this->std->servico->valores;
        $node = $this->dom->createElement('Servico');
        $valnode = $this->dom->createElement('Valores');
        $this->dom->addChild(
            $valnode,
            "ValorServicos",
            number_format($val->valorservicos, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $valnode,
            "ValorDeducoes",
            isset($val->valordeducoes)
                ? number_format($val->valordeducoes, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorPis",
            isset($val->valorpis)
                ? number_format($val->valorpis, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorCofins",
            isset($val->valorcofins)
                ? number_format($val->valorcofins, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorInss",
            isset($val->valorinss)
                ? number_format($val->valorinss, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorIr",
            isset($val->valorir)
                ? number_format($val->valorir, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorCsll",
            isset($val->valorcsll)
                ? number_format($val->valorcsll, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "IssRetido",
            isset($val->issretido) ? $val->issretido : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "ValorIss",
            isset($val->valoriss)
                ? number_format($val->valoriss, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "OutrasRetencoes",
            isset($val->outrasretencoes)
                ? number_format($val->outrasretencoes, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "Aliquota",
            isset($val->aliquota) ? $val->aliquota : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "DescontoIncondicionado",
            isset($val->descontoincondicionado)
                ? number_format($val->descontoincondicionado, 2, '.', '')
                : null,
            false
        );
        $this->dom->addChild(
            $valnode,
            "DescontoCondicionado",
            isset($val->descontocondicionado)
                ? number_format($val->descontocondicionado, 2, '.', '')
                : null,
            false
        );
        $node->appendChild($valnode);

        $this->dom->addChild(
            $node,
            "ItemListaServico",
            $serv->itemlistaservico,
            true
        );
        $this->dom->addChild(
            $node,
            "CodigoTributacaoMunicipio",
            $serv->codigotributacaomunicipio,
            true
        );
        $this->dom->addChild(
            $node,
            "Discriminacao",
            $serv->discriminacao,
            true
        );
        $this->dom->addChild(
            $node,
            "CodigoMunicipio",
            $serv->codigomunicipio,
            true
        );

        if($this->tp == 2)
        {
            $ItServ = $this->dom->createElement('ItensServico');
            $this->dom->addChild(
                $ItServ,
                "Descricao",
                $serv->discriminacao,
                true
            );
            $this->dom->addChild(
                $ItServ,
                "Quantidade",
                1,
                true
            );
            $this->dom->addChild(
                $ItServ,
                "ValorUnitario",
                number_format($val->valorservicos, 2, '.', ''),
                true
            );
            $node->appendChild($ItServ);
        }

        $parent->appendChild($node);
    }

    /**
     * Includes Tomador TAG in parent NODE
     * @param DOMNode $parent
     */
    protected function addTomador(&$parent)
    {
        if (!isset($this->std->tomador)) {
            return;
        }
        $tom = $this->std->tomador;
        $end = $this->std->tomador->endereco;

        $node = $this->dom->createElement('Tomador');
        if(isset($tom->cnpj) || isset($tom->cpf))
        {
            $ide = $this->dom->createElement('IdentificacaoTomador');
            $cpfcnpj = $this->dom->createElement('CpfCnpj');
            if (isset($tom->cnpj)) {
                $this->dom->addChild(
                    $cpfcnpj,
                    "Cnpj",
                    $tom->cnpj,
                    true
                );
            } else {
                $this->dom->addChild(
                    $cpfcnpj,
                    "Cpf",
                    $tom->cpf,
                    true
                );
            }
            $ide->appendChild($cpfcnpj);
            $this->dom->addChild(
                $ide,
                "InscricaoMunicipal",
                isset($tom->inscricaomunicipal) ? $tom->inscricaomunicipal : null,
                false
            );
            $node->appendChild($ide);
        }
        $this->dom->addChild(
            $node,
            "RazaoSocial",
            $tom->razaosocial,
            true
        );
        $endereco = $this->dom->createElement('Endereco');
        $this->dom->addChild(
            $endereco,
            "Endereco",
            $end->endereco,
            true
        );
        $this->dom->addChild(
            $endereco,
            "Numero",
            $end->numero,
            true
        );
        $this->dom->addChild(
            $endereco,
            "Complemento",
            isset($end->complemento) ? $end->complemento : null,
            false
        );
        $this->dom->addChild(
            $endereco,
            "Bairro",
            $end->bairro,
            true
        );
        $this->dom->addChild(
            $endereco,
            "CodigoMunicipio",
            $end->codigomunicipio,
            true
        );
        $this->dom->addChild(
            $endereco,
            "Uf",
            $end->uf,
            true
        );
        $this->dom->addChild(
            $endereco,
            "Cep",
            isset($end->cep) ? $end->cep : null,
            $end->cep,
            false
        );
        $node->appendChild($endereco);
        $parent->appendChild($node);
    }

    /**
     * Includes Intermediario TAG in parent NODE
     * @param DOMNode $parent
     */
    protected function addIntermediario(&$parent)
    {
        if (!isset($this->std->intermediarioservico)) {
            return;
        }
        $int = $this->std->intermediarioservico;
        $node = $this->dom->createElement('IntermediarioServico');
        $this->dom->addChild(
            $node,
            "RazaoSocial",
            $int->razaosocial,
            true
        );
        $cpfcnpj = $this->dom->createElement('CpfCnpj');
        if (isset($int->cnpj)) {
            $this->dom->addChild(
                $cpfcnpj,
                "Cnpj",
                $int->cnpj,
                true
            );
        } else {
            $this->dom->addChild(
                $cpfcnpj,
                "Cpf",
                $int->cpf,
                true
            );
        }
        $node->appendChild($cpfcnpj);
        $this->dom->addChild(
            $node,
            "InscricaoMunicipal",
            $int->inscricaomunicipal,
            false
        );
        $parent->appendChild($node);
    }

    /**
     * Includes Construcao TAG in parent NODE
     * @param DOMNode $parent
     */
    protected function addConstrucao(&$parent)
    {
        if (!isset($this->std->construcaocivil)) {
            return;
        }
        $obra = $this->std->construcaocivil;
        $node = $this->dom->createElement('ContrucaoCivil');
        $this->dom->addChild(
            $node,
            "CodigoObra",
            $obra->codigoobra,
            true
        );
        $this->dom->addChild(
            $node,
            "Art",
            $obra->art,
            true
        );
        $parent->appendChild($node);
    }
}
