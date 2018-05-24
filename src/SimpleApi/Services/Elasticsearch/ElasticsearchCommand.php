<?php

namespace SimpleApi\Services\Elasticsearch;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\LoginMonitoramento;
use SimpleApi\Entity\MensagemPadrao;
use SimpleApi\Entity\Cliente;
use SimpleApi\Entity\ConexaoVideo;
use SimpleApi\Entity\EventoContactIdMonitoramento;
use SimpleApi\Entity\MapaFullarmFulltrack;
use SimpleApi\Entity\MedidorEnergia;
use SimpleApi\Entity\Painel;
use SimpleApi\Entity\PerfilEventoMonitoramento;
use SimpleApi\Entity\Receptor;
use SimpleApi\Entity\Sistema;
use SimpleApi\Entity\Operadora;
use SimpleApi\Entity\MotivoAlarme;
use SimpleApi\Entity\LoginCompany;
use SimpleApi\Entity\GrupoAtendimento;
use SimpleApi\Entity\AnotacoesTemporarias;
use SimpleApi\Services\Elasticsearch\EventoContactIdMonitoramento\ElasticsearchEventoContactIdMonitoramentoService;
use SimpleApi\Services\Elasticsearch\MotivoAlarme\ElasticsearchMotivoAlarmeService;
use SimpleApi\Services\Elasticsearch\PerfilEventoMonitoramento\ElasticsearchPerfilEventoMonitoramentoService;
use SimpleApi\Services\Elasticsearch\Sistema\ElasticsearchSistemaService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use SimpleApi\Services\Elasticsearch\Painel\ElasticsearchPainelService;
use SimpleApi\Services\Elasticsearch\Camera\ElasticsearchCameraService;
use SimpleApi\Services\Elasticsearch\Cliente\ElasticsearchClienteService;
use SimpleApi\Services\Elasticsearch\Receptor\ElasticsearchReceptorService;
use SimpleApi\Services\Elasticsearch\MapaFullarmFulltrack\ElasticsearchMapaService;
use SimpleApi\Services\Elasticsearch\MedidorEnergia\ElasticsearchMedidorEnergiaService;
use SimpleApi\Services\Elasticsearch\Storage\ElasticsearchStorage;
use SimpleApi\Services\Elasticsearch\MensagemPadrao\ElasticsearchMensagemService;
use SimpleApi\Services\Elasticsearch\Operadora\ElasticsearchOperadoraService;
use SimpleApi\Services\Elasticsearch\LoginMonitoramento\ElasticsearchLoginMonitoramentoService;
use SimpleApi\Services\Elasticsearch\GrupoAtendimento\ElasticsearchGrupoAtendimentoService;

class ElasticsearchCommand extends Command
{
    private $em;
    
    protected function configure()
    {
        $this->setName('elasticsearch:index:all')
             ->setDescription('Indexar tudo')
            ->addArgument('indice', InputArgument::OPTIONAL,"Indice monitoramento"  )
            ->setHelp(<<<EOT
O comando <info>%command.name%</info> indexa todos os patchs ex:(Painel, Clientes, Sistema) de todos os monitoramentos ou apenas um

    <info>%command.full_name% {index}</info>
EOT
        );
    }
    
    public function setEm(EntityManager $em){
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $saida = "";

        try {
            $elasticsearchServices = $this->getElasticsearchServices();
            $idCompany             =  $input->getArgument('indice');
            $elasticsearchStorage  = new ElasticsearchStorage($this->em);

            $this->index($elasticsearchStorage, $elasticsearchServices, $idCompany);
            $saida = "Indexado com sucesso!!!";

        } catch (\Exception $e) {
            $saida = $e->getMessage();
        }

        $output->writeln($saida);
    }
    
    private function getElasticsearchServices()
    {
        $services = [];

        $services['login'] = new ElasticsearchLoginMonitoramentoService();
        $services['painel'] = new ElasticsearchPainelService();
        $services['camera'] = new ElasticsearchCameraService();
        $services['cliente'] = new ElasticsearchClienteService();
        $services['sistema'] = new ElasticsearchSistemaService();
        $services['receptor'] = new ElasticsearchReceptorService();
        $services['perfilEvento'] = new ElasticsearchPerfilEventoMonitoramentoService();
        $services['mapaFulltrack'] = new ElasticsearchMapaService();
        $services['medidorEnergia'] = new ElasticsearchMedidorEnergiaService();
        $services['eventoContactId'] = new ElasticsearchEventoContactIdMonitoramentoService();
        $services['mensagem'] = new ElasticsearchMensagemService();
        $services['operadora'] = new ElasticsearchOperadoraService();
        $services['loginMonitoramento'] = new ElasticsearchLoginMonitoramentoService();
        $services['grupoAtendimento'] = new ElasticsearchGrupoAtendimentoService();
        $services['motivoAlarme'] = new ElasticsearchMotivoAlarmeService();

        return $services;
    }

    private function index(ElasticsearchStorage $elasticsearchStorage, $services, $idCompany = null)
    {

        foreach ($elasticsearchStorage->getCameras($idCompany) as $camera)
        {
            $this->indexCameras($services['camera'], $camera);
        }

        foreach ($elasticsearchStorage->getClientes($idCompany) as $cliente)
        {
            $this->indexClientes($services['cliente'],$cliente);
        }

        foreach ($elasticsearchStorage->getPaineis($idCompany) as $painel)
        {
            $this->indexPaineis($services['painel'],$painel);
        }

        foreach ($elasticsearchStorage->getReceptores($idCompany) as $receptor)
        {
            $this->indexReceptores($services['receptor'],$receptor);
        }

        foreach ($elasticsearchStorage->getSistemas($idCompany) as $sistema)
        {
            $this->indexSistemas($services['sistema'],$sistema);
        }

        foreach ($elasticsearchStorage->getMapas($idCompany) as $mapa)
        {
            $this->indexMapas($services['mapaFulltrack'],$mapa);
        }

        foreach ($elasticsearchStorage->getPerfilEvento($idCompany) as $mapa)
        {
            $this->indexPerfilEvento($services['perfilEvento'],$mapa);
        }

        foreach ($elasticsearchStorage->getEventosContactId($idCompany) as $mapa)
        {
            $this->indexEventoContactId($services['eventoContactId'],$mapa);
        }

        foreach ($elasticsearchStorage->getMedidoresEnergia($idCompany) as $medidorEnergia)
        {
            $this->indexMedidoresEnergia($services['medidorEnergia'],$medidorEnergia);
        }
        foreach ($elasticsearchStorage->getMensagem($idCompany) as $mensagem)
        {
            $this->indexMensagem($services['mensagem'], $mensagem);
        }
        foreach ($elasticsearchStorage->getOperadora($idCompany) as $operadora)
        {
            $this->indexOperadora($services['operadora'], $operadora);
        }

        foreach ($elasticsearchStorage->getLoginMonitoramento($idCompany) as $loginMonitoramento)
        {
            $this->indexLoginMonitoramento($services['loginMonitoramento'], $loginMonitoramento);
        }
        foreach ($elasticsearchStorage->getGrupoAtendimento($idCompany) as $grupoAtendimento)
        {
            $this->indexGrupoAtendimento($services['grupoAtendimento'], $grupoAtendimento);
        }
        foreach ($elasticsearchStorage->getMotivoAlarme($idCompany) as $motivoAlarme)
        {
            $this->indexMotivoAlarme($services['motivoAlarme'], $motivoAlarme);
        }
    }


    private function indexPaineis(ElasticsearchPainelService $elasticSearchService, Painel $painel)
    {
        $elasticSearchService->indexar($painel->getMonitoramento()->getId(), $painel->getId(), $painel->toArray());
    }

    private function indexClientes(ElasticsearchClienteService $elasticsearchClienteService, Cliente $cliente)
    {
        $elasticsearchClienteService->indexar($cliente->getMonitoramento()->getId(), $cliente->getId(), $cliente->toArray());
    }

    private function indexSistemas(ElasticsearchSistemaService $elasticsearchSistemaService, Sistema $sistema)
    {
        $elasticsearchSistemaService->indexar($sistema->getMonitoramento()->getId(), $sistema->getId(), $sistema->toFlatArray());
    }

    private function indexCameras(ElasticsearchCameraService $elasticSearchService, ConexaoVideo $conexaoVideo)
    {
        $elasticSearchService->indexar($conexaoVideo->getMonitoramento()->getId(), $conexaoVideo->getId(), $conexaoVideo->toArray());
    }

    private function indexReceptores(ElasticsearchReceptorService $elasticsearchReceptorService, Receptor $receptor)
    {
        $elasticsearchReceptorService->indexar($receptor->getMonitoramento(), $receptor->getId(), $receptor->toArray());
    }

    private function indexMapas(ElasticsearchMapaService $elasticsearchMapaService, MapaFullarmFulltrack $mapaFullarmFulltrack)
    {
        $elasticsearchMapaService->indexar($mapaFullarmFulltrack->getCompany(), $mapaFullarmFulltrack->getId(), $mapaFullarmFulltrack->toArray());
    }

    private function indexMedidoresEnergia(ElasticsearchMedidorEnergiaService $elasticsearchMedidorEnergiaService , MedidorEnergia $medidorEnergia)
    {
        $elasticsearchMedidorEnergiaService->indexar($medidorEnergia->getMonitoramento(), $medidorEnergia->getId(), $medidorEnergia->toArray());
    }
    private function indexMensagem(ElasticsearchMensagemService $elasticsearchMensagemService , MensagemPadrao $mensagem)
    {
        $elasticsearchMensagemService->indexar($mensagem->getMonitoramento()->getId(), $mensagem->getId(), $mensagem->toArray());
    }

    private function indexPerfilEvento(ElasticsearchPerfilEventoMonitoramentoService $monitoramentoService, PerfilEventoMonitoramento $eventoMonitoramento)
    {
        $monitoramentoService->indexar($eventoMonitoramento->getMonitoramento(), $eventoMonitoramento->getId(), $eventoMonitoramento->toArray());
    }

    private function indexEventoContactId(ElasticsearchEventoContactIdMonitoramentoService $monitoramentoService, EventoContactIdMonitoramento $contactIdMonitoramento)
    {
        $monitoramentoService->indexar($contactIdMonitoramento->getMonitoramento(), $contactIdMonitoramento->getId(), $contactIdMonitoramento->toArray());
    }
    private function indexOperadora(ElasticsearchOperadoraService $elasticsearchOperadoraService , Operadora $operadora)
    {
        $elasticsearchOperadoraService->indexar($operadora->getMonitoramento()->getId(), $operadora->getId(), $operadora->toArray());
    }
    private function indexMotivoAlarme(ElasticsearchMotivoAlarmeService $elasticsearchMotivoAlarmeService , MotivoAlarme $motivoAlarme)
    {
        $elasticsearchMotivoAlarmeService->indexar($motivoAlarme->getMonitoramento()->getId(), $motivoAlarme->getId(), $motivoAlarme->toArray());
    }
    private function indexLoginMonitoramento(ElasticsearchLoginMonitoramentoService $elasticsearchLoginMonitoramentoService, LoginCompany $loginMonitoramento)
    {
        $elasticsearchLoginMonitoramentoService->indexar($loginMonitoramento->getMonitoramento()->getId(), $loginMonitoramento->getId(), $loginMonitoramento->toArray());
    }
    private function indexGrupoAtendimento(ElasticsearchGrupoAtendimentoService $elasticsearchGrupoAtendimentoService, GrupoAtendimento $grupoAtendimento)
    {
        $elasticsearchGrupoAtendimentoService->indexar($grupoAtendimento->getMonitoramento()->getId(), $grupoAtendimento->getId(), $grupoAtendimento->toArray());
    }
}
