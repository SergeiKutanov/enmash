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

    private $em;

    protected function configure() {
        $this
            ->setName('catalog:import')
            ->setDescription('Import catalog from excel file');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //test stuff
        /* @var $catalogImporter CatalogImporter */
        $catalogImporter = $this
            ->getContainer()
            ->get('enmash_store.catalog_importer');
        $catalogImporter->importCategories(); die();

        $this->em = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $output->write('Import started' . PHP_EOL);

        $file = $this->getFile();

        if (!$file) {
            $output->writeln(
                'PHPExcel object was not created' . PHP_EOL
            );
        }

        $this->parseManufacturers($file);

        $this->parseGoods($file);

        $output->writeln('Import is done');

    }

    protected function getFile() {
        /* @var $phpExcelObject PHPExcel */
        try {
            $phpExcelObject = $this
                ->getContainer()
                ->get('phpexcel')
                ->createPHPExcelObject(self::PATH . self::FILENAME);
        } catch (\Exception $ex) {
            return null;
        }

        return $phpExcelObject;

//
//        var_dump($phpExcelObject->getActiveSheet()->getCellByColumnAndRow(0, 1)->getValue());

    }

    protected function parseLine(\PHPExcel_Worksheet $sheet, $lineNumber) {
        try {

            $productSku = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $lineNumber)
                ->getValue();

            $product = $this->em
                ->getRepository('EnmashStoreBundle:Product')
                ->findOneBySku($productSku);

            if (!$product) {
                $product = new Product();
                $product->setSku($productSku);
            }

            $product->setAcronym(
                $sheet
                    ->getCellByColumnAndRow(self::PRODUCT_ACRONYM_COLUMN, $lineNumber)
                    ->getValue()
            );

            $product->setMansku(
                $sheet
                    ->getCellByColumnAndRow(self::PRODUCT_MAN_SKU, $lineNumber)
                    ->getValue()
            );

            $manufacturerName = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_MAN, $lineNumber)
                ->getValue();
            $manufacturer = $this->em
                ->getRepository('EnmashStoreBundle:Manufacturer')
                ->findOneByName($manufacturerName);
            if (!$manufacturer) {
                throw new \Exception("Manufacturer for line: " . $lineNumber . ' not found');
            }

        } catch (\Exception $ex) {
            return null;
        }

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    protected function parseManufacturers(PHPExcel $file) {
        $file->setActiveSheetIndexByName(self::MANUFACTURERS_LIST_NAME);

        $sheet = $file->getActiveSheet();

        $manufactorersRepository = $this->em->getRepository('EnmashStoreBundle:Manufacturer');
        $rowIndex = 2;
        while ($sheet->cellExistsByColumnAndRow(self::MANUFACTURERS_NAME_COLUMN, $rowIndex)) {
            $manufactorerTitle = $sheet
                ->getCellByColumnAndRow(self::MANUFACTURERS_NAME_COLUMN, $rowIndex)
                ->getValue();

            $manufacturer = $manufactorersRepository
                ->findOneByName($manufactorerTitle);

            if (!$manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($manufactorerTitle);

                $manufacturerSite = $sheet
                    ->getCellByColumnAndRow(self::MANUFACTURERS_SITE_COLUMN, $rowIndex)
                    ->getValue();
                $manufacturer->setWebsite($manufacturerSite);

                $this->em->persist($manufacturer);
            }
            $rowIndex++;
        }

        $this->em->flush();

    }

    private function parseGoods(PHPExcel $file) {
        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);

        $rowIndex = 2;
        $goodsSheet = $file->getActiveSheet();
        while ($goodsSheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)) {
            $this->parseLine($goodsSheet, $rowIndex);
            $rowIndex++;
        }

    }

} 