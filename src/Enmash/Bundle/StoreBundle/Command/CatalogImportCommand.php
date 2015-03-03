<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/27/14
 * Time: 6:45 PM
 */

namespace Enmash\Bundle\StoreBundle\Command;


use Enmash\Bundle\StoreBundle\Component\CatalogImporter;
use Enmash\Bundle\StoreBundle\Entity\Manufacturer;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPExcel;

class CatalogImportCommand extends ContainerAwareCommand {

    const PATH = 'web/catalog/';
    const FILENAME = 'catalog.ods';
    const GOODS_LIST_NAME = 'goods';
    const MANUFACTURERS_LIST_NAME = 'manufacturers';

    //cell coordinates
    const MANUFACTURERS_NAME_COLUMN = 0;
    const MANUFACTURERS_SITE_COLUMN = 1;

    const PRODUCT_CODE_COLUMN = 0;
    const PRODUCT_ACRONYM_COLUMN = 2;
    const PRODUCT_MAN_SKU = 3;
    const PRODUCT_MAN = 4;

    const OPTION_MODE_ALL = 'all';
    const OPTION_MODE_MANUFACTURERS = 'manufacturers';
    const OPTION_MODE_CATEGORIES = 'categories';
    const OPTION_MODE_GOODS = 'goods';
    const OPTION_MODE_ANALOGS_AND_SIMILARS = 'analogs';
    const OPTION_MODE_CLEAR = 'clear';
    const OPTION_MODE_FLUSH_CATEGORIES = 'flush-categories';
    const OPTION_MODE_FIX_PHOTO_FILE = 'fix-photo';
    const OPTION_MODE_FIX_ANALOGS = 'fix-analogs';
    const OPTION_MODE_PHOTOS_ONLY = 'photos-only';

    private $em;
    /* @var $catalogImporter CatalogImporter */
    private $catalogImporter;

    protected function configure() {
        $this
            ->setName('catalog:import')
            ->setDescription('Import catalog from excel file')
            ->addOption(
                'mode',
                'm',
                InputArgument::OPTIONAL,
                'Import type: ' . self::OPTION_MODE_ALL . '|' . self::OPTION_MODE_MANUFACTURERS . '|' . self::OPTION_MODE_CATEGORIES . '|' . self::OPTION_MODE_GOODS . '|' . self::OPTION_MODE_CLEAR . '|' .
                self::OPTION_MODE_ALL . ' | ' . self::OPTION_MODE_FLUSH_CATEGORIES . '|' . self::OPTION_MODE_PHOTOS_ONLY
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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $file = $this->getFile();

        if (!$file) {
            $output->writeln(
                'PHPExcel object was not created' . PHP_EOL
            );
        }

        $this->em = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->catalogImporter = $this
            ->getContainer()
            ->get('enmash_store.catalog_importer');

        $mode = $input->getOption('mode');
        $offset = (int) $input->getOption('offset');
        $limit = (int) $input->getOption('limit');

        switch ($mode) {
            case self::OPTION_MODE_ALL:
                $output->writeln('Full catalog import started');
                $output->writeln('Manufacturers catalog import started');
                $this->catalogImporter->importManufacturers($file);
                $output->writeln('Categories catalog import started');
                $this->catalogImporter->importCategories($file);
                $output->writeln('Goods catalog import started');
                $this->catalogImporter->importGoods($file);
                break;
            case self::OPTION_MODE_MANUFACTURERS:
                $output->writeln('Manufacturers catalog import started');
                $this
                    ->catalogImporter
                    ->importManufacturers($file);
                break;
            case self::OPTION_MODE_CATEGORIES:
                $output->writeln('Categories catalog import started');
                $this
                    ->catalogImporter
                    ->importCategories($file);
                break;
            case self::OPTION_MODE_GOODS:
                $output->writeln('Goods catalog import started');
                $this
                    ->catalogImporter
                    ->importGoods($file, $offset, $limit);
                break;
            case self::OPTION_MODE_ANALOGS_AND_SIMILARS:
                $output->writeln('Analogs and similars catalog import started');
                $this
                    ->catalogImporter
                    ->importAnalogsAndSimilarGoods($file, $offset, $limit);
                break;
            case self::OPTION_MODE_CLEAR:
                $output->writeln('Removing unused stuff');
                $this
                    ->catalogImporter
                    ->removeUnusedStuff($file);
                break;
            case self::OPTION_MODE_FLUSH_CATEGORIES:
                $output->writeln('Flushing Categories');
                $this->
                    catalogImporter
                    ->flushCategories();
                break;
            case self::OPTION_MODE_PHOTOS_ONLY:
                $output->writeln('Storing Photos Only');
                $this
                    ->catalogImporter
                    ->importGoods($file, $offset, $limit);
                break;
            case self::OPTION_MODE_FIX_PHOTO_FILE:
                $this
                    ->catalogImporter
                    ->fixPhotoFile(
                        $file
                    );
                break;
            case self::OPTION_MODE_FIX_ANALOGS:
                $this->catalogImporter
                    ->fixAnalogs(
                        $file,
                        $this->getFile(self::PATH . '/legacy_catalog.ods')
                    );
                break;
            default:
                $output->writeln('Invalid mode');
                break;
        }
        $output->writeln('Import is done');
    }

    protected function getFile($path = null) {
        if (!$path) {
            $path = self::PATH . self::FILENAME;
        }
        /* @var $phpExcelObject PHPExcel */
        try {
            $phpExcelObject = $this
                ->getContainer()
                ->get('phpexcel')
                ->createPHPExcelObject($path);
        } catch (\Exception $ex) {
            return null;
        }

        return $phpExcelObject;

    }

} 