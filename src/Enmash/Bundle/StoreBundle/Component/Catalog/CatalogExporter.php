<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 30.01.15
 * Time: 9:39
 */

namespace Enmash\Bundle\StoreBundle\Component\Catalog;


use Application\Sonata\MediaBundle\Entity\Gallery;
use Enmash\Bundle\StoreBundle\Component\Catalog\Catalog;
use Enmash\Bundle\StoreBundle\Entity\Product;

class CatalogExporter extends Catalog {

    const FILE_CREATOR = 'Energomash Catalog Creator';
    const FILE_TITLE = 'Energomash Catalog - ';
    const SHEET_NAME = 'Goods';

    public function exportGoods($limit = null, $offset = null) {

        $file = $this->createFile();

        $goods = $this->getGoodsForProccessing($limit, $offset);

        foreach ($goods as $key => $good) {
            echo "Importing $key row" . PHP_EOL;
            $rowIndex = $key + 2;
            /* @var $good \Enmash\Bundle\StoreBundle\Entity\Product */
            $this->fillRow($good, $rowIndex, $file);
        }

        $this->saveToFile($file);
    }

    /**
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    private function createFile() {
        $file = new \PHPExcel();

        //set some properties
        $file->getProperties()->setCreator(self::FILE_CREATOR);
        $file->getProperties()->setCreated();

        $file->setActiveSheetIndex(0);
        $file->getActiveSheet()->setTitle(self::SHEET_NAME);

        $this->createSheetHeaders($file);

        return $file;

    }

    private function saveToFile(\PHPExcel $file) {
        $writer = new \PHPExcel_Writer_Excel2007($file);
        $writer->save(realpath('') . '/web/catalog/' . self::FILE_TITLE . date('Y-m-d') . '.xlsx');
    }

    private function createSheetHeaders(\PHPExcel $file)
    {
        $sheet = $file->getActiveSheet();
        $row = 1;
        $sheet->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $row)->setValue('код');
        $sheet->getCellByColumnAndRow(self::PRODUCT_CATEGORY_COLUMN, $row)->setValue('группа');
        $sheet->getCellByColumnAndRow(self::PRODUCT_NAME_COLUMN, $row)->setValue('название');
        $sheet->getCellByColumnAndRow(self::PRODUCT_ACRONYM_COLUMN, $row)->setValue('наименование');
        $sheet->getCellByColumnAndRow(self::PRODUCT_MAN_SKU, $row)->setValue('артикул изготовителя');
        $sheet->getCellByColumnAndRow(self::PRODUCT_MAN, $row)->setValue('бренд/изготовитель');
        $sheet->getCellByColumnAndRow(self::PRODUCT_ANALOGS_COLUMN, $row)->setValue('аналоги');
        $sheet->getCellByColumnAndRow(self::PRODUCT_SIMILAR_COLUMN, $row)->setValue('дополнительные товары');
        $sheet->getCellByColumnAndRow(self::PRODUCT_PHOTO, $row)->setValue('фото');
        $sheet->getCellByColumnAndRow(self::PRODUCT_DESCRIPTION_COLUMN, $row)->setValue('описание');
        $sheet->getCellByColumnAndRow(self::PRODUCT_CERTIFICATE_COLUMN, $row)->setValue('сертификат');
    }

    private function fillRow(Product $good, $rowIndex, \PHPExcel $file)
    {
        $sheet = $file->getActiveSheet();

        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)
            ->setValue(
                $good->getSku()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_CATEGORY_COLUMN, $rowIndex)
            ->setValue(
                $good->getCategory()->getName()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_NAME_COLUMN, $rowIndex)
            ->setValue(
                $good->getName()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_ACRONYM_COLUMN, $rowIndex)
            ->setValue(
                $good->getAcronym()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_MAN_SKU, $rowIndex)
            ->setValue(
                $good->getMansku()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_MAN, $rowIndex)
            ->setValue(
                $good->getManufacturer()->getName()
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_ANALOGS_COLUMN, $rowIndex)
            ->setValue(
                $this->getStringFromGroupOfProducts($good->getAnalogs())
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_SIMILAR_COLUMN, $rowIndex)
            ->setValue(
                $this->getStringFromGroupOfProducts($good->getSimilars())
            );
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_PHOTO, $rowIndex)
            ->setValue(
                $this->getPhotoString($good->getProductImages())
            );
        //todo store product's description
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_DESCRIPTION_COLUMN, $rowIndex)
            ->setValue(null);
        $sheet
            ->getCellByColumnAndRow(self::PRODUCT_CERTIFICATE_COLUMN, $rowIndex)
            ->setValue(
                $this->getPhotoString($good->getCertificates())
            );
    }

    private function getStringFromGroupOfProducts($products) {
        $count = count($products) - 1;
        $strValue = '';
        foreach ($products as $index => $product) {
            /* @var $product Product */
            $strValue .= $product->getSku();
            if ($index <  $count) $strValue .= ', ';
        }
        return $strValue;
    }

    private function getPhotoString($productImages)
    {
        if (!$productImages) return '';
        $count = count($productImages) - 1;
        $strValue = '';
        foreach ($productImages->getGalleryHasMedias() as $index => $photo) {
            /* @var $photo \Application\Sonata\MediaBundle\Entity\GalleryHasMedia */
            $strValue .= $photo->getMedia()->getName();
            if ($index < $count) $strValue .= ', ';
        }
        return $strValue;
    }

    private function getGoodsForProccessing($limit, $offset)
    {
        $goods = $this
            ->em
            ->getRepository('EnmashStoreBundle:Product');
        if ($offset && $limit) {
            return $goods
                ->findBy(
                    array(),
                    null,
                    $limit,
                    $offset
                );
        }

        if ($offset) {
            return $goods
                ->findBy(
                    array(),
                    null,
                    null,
                    $offset
                );
        }

        if ($limit) {
            return $goods
                ->findBy(
                    array(),
                    null,
                    $limit,
                    null
                );
        }

        return $goods->findAll();
    }

}