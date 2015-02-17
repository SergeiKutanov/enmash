<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 30.01.15
 * Time: 9:53
 */

namespace Enmash\Bundle\StoreBundle\Command;


use Doctrine\ORM\EntityManager;
use Enmash\Bundle\StoreBundle\Component\CatalogExporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogExportCommand extends ContainerAwareCommand {

    const OPTION_MODE_GOODS = 'goods';

    /* @var $em EntityManager */
    private $em;
    /* @var $catalogExporter CatalogExporter */
    private $catalogExporter;

    protected function configure() {
        $this
            ->setName('catalog:export')
            ->setDescription('Export catalog from database')
            ->addOption(
                'mode',
                'm',
                InputArgument::OPTIONAL,
                'Export type: ' . self::OPTION_MODE_GOODS
            )
            ->addOption(
                'offset',
                'o',
                InputArgument::OPTIONAL,
                'Line offset'
            )
            ->addOption(
                'limit',
                'l',
                InputArgument::OPTIONAL,
                'limit'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->catalogExporter = $this
            ->getContainer()
            ->get('enmash_store.catalog_exporter');

        list($mode, $limit, $offset) = ($this->getOptions($input));

        switch ($mode) {
            case self::OPTION_MODE_GOODS:
                $this->catalogExporter->exportGoods($limit, $offset);
                break;
            default:
                $this->catalogExporter->exportGoods($limit, $offset);
                break;
        }

    }

    private function getOptions(InputInterface $inputInterface) {
        $mode = $inputInterface
            ->getOption('mode');
        $limit = $inputInterface
            ->getOption('limit');
        $offset = $inputInterface
            ->getOption('offset');

        return array($mode, $limit, $offset);
    }

}