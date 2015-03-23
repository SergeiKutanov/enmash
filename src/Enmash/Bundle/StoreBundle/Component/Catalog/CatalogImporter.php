<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/30/14
 * Time: 9:35 AM
 */

namespace Enmash\Bundle\StoreBundle\Component\Catalog;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Application\Sonata\MediaBundle\Entity\Media;
use Enmash\Bundle\StoreBundle\Entity\Category;
use Enmash\Bundle\StoreBundle\Entity\Manufacturer;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CatalogImporter extends Catalog{

    private static $legitFileExtensions = array(
        'jpg',
        'png',
        'jpeg'
    );

    public function importFullCatalog(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        //importing three level categories
        $this->importFirstLevelTree($file);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_2_LIST_NAME);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_3_LIST_NAME);

        //removing unused categories (i.e. those which are not in ods file)
        $this->removeUnusedCategories($file);

        //importing manufacturers
        $this->importManufacturers($file);

        //removing unused manufacturers
        $this->removeUnusedManufacturers($file);


    }

    private function importFirstLevelTree(\PHPExcel $file) {
        $file->setActiveSheetIndexByName(self::TREE_LEVEL_1_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $categoriesRepository = $this->em->getRepository('EnmashStoreBundle:Category');
        $rowIndex = 2;
        while ($sheet->cellExistsByColumnAndRow(self::TREE_LEVEL_1_CATEGORY_NAME_COLUMN, $rowIndex)) {
            $categoryTitle = $sheet
                ->getCellByColumnAndRow(self::TREE_LEVEL_1_CATEGORY_NAME_COLUMN, $rowIndex)
                ->getValue();
            $categorySort = $sheet
                ->getCellByColumnAndRow(self::TREE_LEVEL_1_SORT_COLUMN, $rowIndex)
                ->getValue();
            /* @var $category \Enmash\Bundle\StoreBundle\Entity\Category */
            $category = $categoriesRepository->findOneByName($categoryTitle);
            if (!$category) {
                $category = new Category();
            }

            $category->setName($categoryTitle);
            $category->setSort($categorySort);
            $this->em->persist($category);

            $rowIndex++;
        }
        $this->em->flush();
    }

    public function importAnalogsAndSimilarGoods(\PHPExcel $file, $offset = null, $limit = null) {
        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $rowIndex = 2;
        if (is_int($offset)) {
            $rowIndex += $offset;
        }
        $goodsRepository = $this->em->getRepository('EnmashStoreBundle:Product');

        if (!$limit) {
            $limit = 9999;
        }

        while ($sheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex) && $limit > 0) {
            $limit--;
            $productCode = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)
                ->getValue();
            echo $rowIndex .': Working on analogs for ' . $productCode . PHP_EOL;
            $analogs = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_ANALOGS_COLUMN, $rowIndex)
                ->getValue();
            $similars = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_SIMILAR_COLUMN, $rowIndex)
                ->getValue();
            /* @var $good Product */
            $good = $this
                ->em
                ->getRepository('EnmashStoreBundle:Product')
                ->findOneBy(
                    array(
                        'sku'   => $productCode
                    )
                );
            if (!$good) {
                throw new NotFoundResourceException('Product -(' . $productCode  .')- not found while working on analogs');
            }

            if (count($good->getAnalogs())) {
                foreach ($good->getAnalogs() as $analog) {
                    $good->removeAnalog($analog);
                }
                $this->em->flush();
            }

            if (count($good->getSimilars())) {
                foreach ($good->getSimilars() as $similar) {
                    $good->removeSimilar($similar);
                }
                $this->em->flush();
            }


            if ($analogs) {
                $analogs = explode(',', $analogs);
                foreach ($analogs as $analog) {
                    $analog = trim($analog);
                    $analogGood = $this
                        ->em
                        ->getRepository('EnmashStoreBundle:Product')
                        ->findOneBy(
                            array(
                                'sku'   => $analog
                            )
                        );
                    if ($analogGood) {
                        $good->addAnalog($analogGood);
                    }
                }
            }

            if ($similars) {
                $similars = explode(',', $similars);
                foreach ($similars as $similar) {
                    $similar = trim($similar);
                    $similarGood = $this
                        ->em
                        ->getRepository('EnmashStoreBundle:Product')
                        ->findOneBy(
                            array(
                                'sku'   => $similar
                            )
                        );
                    if ($similarGood) {
                        $good->addSimilar($similarGood);
                    }
                }
            }

            $rowIndex++;
            $this->em->flush();
            $this->em->clear();
        }
    }

    private function importSubLevelCategories(\PHPExcel $file, $levelSheetName) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName($levelSheetName);
        $sheet = $file->getActiveSheet();

        $categoryRepository = $this->em->getRepository('EnmashStoreBundle:Category');
        $rowIndex = 2;

        while($sheet->cellExistsByColumnAndRow(self::TREE_SUBLEVEL_CATEGORY_NAME_COLUMN, $rowIndex)) {

            $categoryTitle = $sheet
                ->getCellByColumnAndRow(self::TREE_SUBLEVEL_CATEGORY_NAME_COLUMN, $rowIndex)
                ->getValue();
            $categorySort = $sheet
                ->getCellByColumnAndRow(self::TREE_LEVEL_1_SORT_COLUMN, $rowIndex)
                ->getValue();

            $parentCategoryTitle = $sheet
                ->getCellByColumnAndRow(self::TREE_SUBLEVEL_PARENT_CATEGORY_NAME_COLUMN, $rowIndex)
                ->getValue();
            $parentCategory = $categoryRepository
                ->findOneByName($parentCategoryTitle);

            // don't take in consideration categories with the same name
            if ($parentCategoryTitle == $categoryTitle) {
                    $rowIndex++;
                    continue;
                }

            if (!$parentCategory) {
                throw new NotFoundHttpException('Parent category for ' . $categoryTitle . ' not found');
            }

            $category = $categoryRepository
                ->findOneByName($categoryTitle);
            if (!$category) {
                $category = new Category();
            }
            $category->setName($categoryTitle);
            $category->setSort($categorySort);
            $category->setParentCategory($parentCategory);
            $this->em->persist($category);

            $rowIndex++;
        }

        $this->em->flush();
    }

    public function removeUnusedCategories(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName(self::TREE_FULL_LIST);
        $sheet = $file->getActiveSheet();

        $categoriesArray = array();
        $rowIndex = 1;
        $columnIndex = 0;

        while ($sheet->cellExistsByColumnAndRow($columnIndex, $rowIndex)) {
            $categoryName = $sheet
                ->getCellByColumnAndRow($columnIndex, $rowIndex)
                ->getOldCalculatedValue();
            $categoriesArray[] = $categoryName;
            $rowIndex++;
            if (!$sheet->cellExistsByColumnAndRow($columnIndex, $rowIndex)) {
                $columnIndex++;
                $rowIndex = 1;
            }
        }

        $categories = $this
            ->em
            ->getRepository('EnmashStoreBundle:Category')
            ->findAll();

        foreach ($categories as $category) {
            /* @var $category Category */
            if (!in_array($category->getName(), $categoriesArray)) {
                $this->em->remove($category);
            }
        }

        $this->em->flush();

    }

    public function importManufacturers(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

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

    public function removeUnusedManufacturers(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName(self::MANUFACTURERS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $manufacturersArray = array();
        $rowIndex = 2;
        while ($sheet->cellExistsByColumnAndRow(self::MANUFACTURERS_NAME_COLUMN, $rowIndex)) {
            $manufacturerName = $sheet
                ->getCellByColumnAndRow(self::MANUFACTURERS_NAME_COLUMN, $rowIndex);
            $manufacturersArray[] = $manufacturerName;
            $rowIndex++;
        }

        $manufacturers = $this
            ->em
            ->getRepository('EnmashStoreBundle:Manufacturer')
            ->findAll();

        foreach ($manufacturers as $manufacturer) {
            /* @var $manufacturer Manufacturer */
            if (!in_array($manufacturer->getName(), $manufacturersArray)) {
                $this->em->remove($manufacturer);
            }
        }

        $this->em->flush();

    }

    public function importGoods(\PHPExcel $file = null, $offset = null, $limit = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $rowIndex = 2;
        if (is_int($offset)) {
            $rowIndex += $offset;
        }
        $goodsRepository = $this->em->getRepository('EnmashStoreBundle:Product');

        if (!$limit) {
            $limit = 9999;
        }

        $timeCounterStart = time();
        while ($sheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex) && $limit > 0) {
            $limit--;
            echo $rowIndex . ' products imported' . PHP_EOL;
            $productCode = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)
                ->getValue();

            //product sku | code
            $product = $goodsRepository->findOneBySku($productCode);
            if (!$product) {
                $product = new Product();
                $product->setSku($productCode);
            }

            $productSort = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_SORT_COLUMN, $rowIndex)
                ->getValue();
            $product->setSort($productSort);

            //product category
            $productCategoryName = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CATEGORY_COLUMN, $rowIndex)
                ->getValue();

            $category = $this
                ->em
                ->getRepository('EnmashStoreBundle:Category')
                ->findOneByName($productCategoryName);
            if (!$category) {
                throw new NotFoundHttpException('Category - ' . $productCategoryName . ' - for product - ' . $product->getSku() . ' - not found.');
            }
            $product->setCategory($category);

            //product name
            $productName = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_NAME_COLUMN, $rowIndex)
                ->getValue();
            $product->setName($productName);

            //product acronym
            $acronym = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_ACRONYM_COLUMN, $rowIndex)
                ->getValue();
            if (!$acronym) {
                $acronym = $productName;
            }
            $product->setAcronym($acronym);

            //manufacturer's sku
            $manSku = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_MAN_SKU, $rowIndex)
                ->getValue();
            $product->setMansku($manSku);

            //manufacturer
            $manufacturerName = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_MAN, $rowIndex)
                ->getValue();
            if (!$manufacturerName) {
                $manufacturerName = Product::NO_MANUFACTURER;
            }
            $manufacturer = $this
                ->em
                ->getRepository('EnmashStoreBundle:Manufacturer')
                ->findOneByName($manufacturerName);
            if (!$manufacturer) {
                throw new NotFoundHttpException('Manufacturer - ' . $manufacturerName . ' - for product - ' . $product->getSku() . ' - not found.');
            }
            $product->setManufacturer($manufacturer);
            //photo
            $fileNames = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_PHOTO, $rowIndex)
                ->getValue();
            $photosArray = array();
            if ($fileNames !== null) {
                $fileNames = explode(',', $fileNames);
                foreach($fileNames as $fileName) {
                    $photo = $this->getPhoto($fileName);
                    if ($photo) {
                        $photosArray[] = $photo;
                    }

                }
            }

            if ($photosArray) {
                //create gallery
                $gallery = $product->getProductImages();
                if (!$gallery) {
                    $gallery = new Gallery();
                    $gallery->setName($product->getSku());
                    $gallery->setContext('productimage');
                    $gallery->setDefaultFormat('productimage_small');
                    $gallery->setEnabled(true);
                }
                foreach($photosArray as $photo) {
                    /* @var $photo Media */
                    $galleryHasMedias = $gallery->getGalleryHasMedias();
                    $added = false;
                    foreach ($galleryHasMedias as $galleryHasMedia) {
                        /* @var $galleryHasMedia GalleryHasMedia */
                        if ($galleryHasMedia->getMedia()->getId() == $photo->getId()) {
                            $added = true;
                            break;
                        }
                    }

                    if (!$added) {
                        $galleryHasMedia = new GalleryHasMedia();
                        $galleryHasMedia->setMedia($photo);
                        $galleryHasMedia->setGallery($gallery);
                        $this->em->persist($galleryHasMedia);
                    }
                }
                $product->setProductImages($gallery);
            }


            $this->em->persist($product);
            $this->em->flush();
            $this->em->clear();
            $rowIndex++;

        }
        $timeCounter = time() - $timeCounterStart;
        echo "Done in $timeCounter seconds" . PHP_EOL;

//        $this->importAnalogsAndSimilarGoods($file, $offset, $copyLimit);

    }

    protected function getPhotoFilename($filename) {

        $validFileName = null;
        $dir = self::PATH . self::PATH_PHOTO;

        if (pathinfo($dir . $filename, PATHINFO_EXTENSION)) {
            if (is_file($dir . $filename)) return $filename;
            return null;
        }

        foreach (self::$legitFileExtensions as $ext) {
            if (is_file($dir . $filename . '.' . $ext)) {
                return $filename . '.' . $ext;
            }
        }

        return null;
    }

    protected function getPhoto($fileName) {
        $fileName = trim($fileName);

        $fileName = $this->getPhotoFilename($fileName);

        if ($fileName) {
            //todo think of a different way of getting context parameter
            $media = $this
                ->em
                ->getRepository('ApplicationSonataMediaBundle:Media')
                ->findOneBy(
                    array(
                        'name'      => $fileName,
                        'context'  => 'productimage'
                    )
                );
            if ($media) {
                return $media;
            }

            $media = new Media();
            $filePath = realpath('') . '/' . self::PATH . self::PATH_PHOTO . $fileName;
            $media->setBinaryContent($filePath);
            $media->setContext('productimage');
            $media->setProviderName('sonata.media.provider.image');

            return $media;

        }

        return null;

    }

    public function removeUnusedStuff(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $this->removeUnusedGoods($file);
        $this->removeUnusedCategories($file);
        $this->removeUnusedManufacturers($file);
    }

    public function removeUnusedGoods(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $goodsSkuArray = array();
        $rowIndex = 2;
        while ($sheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)) {
            $goodSku = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex);
            $goodsSkuArray[] = $goodSku;
            $rowIndex++;
        }

        $goods = $this
            ->em
            ->getRepository('EnmashStoreBundle:Product')
            ->findAll();

        foreach ($goods as $good) {
            /* @var $good Product  */
            if (!in_array($good->getSku(), $goodsSkuArray)) {
                $this->em->remove($good);
            }
        }
        $this->em->flush();

    }

    public function importCategories(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $this->importFirstLevelTree($file);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_2_LIST_NAME);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_3_LIST_NAME);

    }

    public function flushCategories() {
        $categories = $this->em->getRepository('EnmashStoreBundle:Category')->findBy(
            array(
                'parentCategory'    => null
            )
        );
        foreach($categories as $category) {
            $this->em->remove($category);
        }
        $this->em->flush();
    }

    public function fixPhotoFile(\PHPExcel $file)
    {

        $file->setActiveSheetIndex();

        $photoFiles = scandir(realpath('') . '/' . self::PATH . self::PATH_PHOTO);

        $originalSheet = $file->getActiveSheet();

        $rowIndex = 1;
        while ($originalSheet->cellExistsByColumnAndRow(0, $rowIndex)) {
            $goodSku = $originalSheet
                ->getCellByColumnAndRow(0, $rowIndex)
                ->getValue();

            $goodPhoto = $originalSheet
                ->getCellByColumnAndRow(self::PRODUCT_PHOTO, $rowIndex)
                ->getValue();

            echo $rowIndex . ': ' . $goodSku . PHP_EOL;
            foreach ($photoFiles as $photo) {
                if (is_file(realpath('') . '/' . self::PATH . self::PATH_PHOTO . '/' . $photo)) {
                    $filename = basename($photo);
                    if ($filename == $goodSku) {
                        echo 'Found photo file: ' . $filename . PHP_EOL;
                        $filename = ($goodPhoto) ? $filename . ', ' . $goodPhoto : $filename;
                        echo 'Stored new value: ' . $filename . PHP_EOL;
                        $originalSheet
                            ->getCellByColumnAndRow(self::PRODUCT_PHOTO, $rowIndex)
                            ->setValue($filename);
                    }
                }
            }


            $rowIndex++;
        }

        $this->saveFile($file);

    }

    public function transferPhotosFromLegacyCatalog(\PHPExcel $currentCatalogFile, \PHPExcel $legacyCatalogFile)
    {
        $currentCatalogFile->setActiveSheetIndex();
        $legacyCatalogFile->setActiveSheetIndexByName('catalog');
    }

    public function fixAnalogs(\PHPExcel $file, \PHPExcel $legacyFile) {
        $file->setActiveSheetIndex();
        $legacyFile->setActiveSheetIndexByName('analogs');

        $originalSheet = $file->getActiveSheet();
        $legacyFileSheet = $legacyFile->getActiveSheet();

        $rowIndex = 2;
        while ($originalSheet->cellExistsByColumnAndRow(0, $rowIndex)) {
            $sku = $originalSheet->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)->getValue();

            echo "$rowIndex: Searching for $sku" . PHP_EOL;
            $legacyCellValue = $this->getLegacyAnalogAndSimilarCellValue($sku, 7, $legacyFileSheet);

            if ($legacyCellValue['analogs'] && $legacyCellValue['analogs'] != '') {
                echo "Found analogs are: " . $legacyCellValue['analogs'] . PHP_EOL;
                $analogsCell = $originalSheet->getCellByColumnAndRow(self::PRODUCT_ANALOGS_COLUMN, $rowIndex);
                $analogsCell->setValue((string)$legacyCellValue['analogs']);
            }

            if ($legacyCellValue['similars'] && $legacyCellValue['similars'] != '') {
                echo "Found similars are: " . $legacyCellValue['similars'] . PHP_EOL;
                $analogsCell = $originalSheet->getCellByColumnAndRow(self::PRODUCT_SIMILAR_COLUMN, $rowIndex);
                $analogsCell->setValue((string)$legacyCellValue['similars']);
            }

            $rowIndex++;
        }

        $this->saveFile($file);

    }

    private function saveFile(\PHPExcel $file)
    {
        $writer = new \PHPExcel_Writer_Excel2007($file);
        $writer->save(realpath('') . '/web/catalog/demo' . date('Y-m-d') . '.xlsx');
    }

    private function getLegacyAnalogAndSimilarCellValue($sku, $int, \PHPExcel_Worksheet $legacySheet)
    {
        $result = array(
            'analogs'   => null,
            'similars'  => null
        );
        $rowIndex = 1;
        while ($legacySheet->cellExistsByColumnAndRow(0, $rowIndex)) {
            $legacySku = $legacySheet->getCellByColumnAndRow(1, $rowIndex)->getValue();
            if ($legacySku == $sku) {
                return array(
                    'analogs'   => $legacySheet->getCellByColumnAndRow(7, $rowIndex),
                    'similars'  => $legacySheet->getCellByColumnAndRow(8, $rowIndex)
                );
            }
            $rowIndex++;
        }

        return $result;
    }

    public function checkMissingPhotos(\PHPExcel $file)
    {
        if (!$file) $file = $this->getFile();

        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $newFile = new \PHPExcel();
        $newSheet = $newFile->getActiveSheet();

        $rowIndex = 2;
        $newSheetCounter = 1;
        while ($sheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)) {

            $sku = $sheet->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)->getValue();
            echo "Checking $sku: ";

            $photos = $sheet->getCellByColumnAndRow(self::PRODUCT_PHOTO, $rowIndex)->getValue();
            if ($photos !== null) {
                $fileNames = explode(',', $photos);
                foreach($fileNames as $fileName) {
                    $photo = $this->getPhotoFilename($fileName);
                    if (!$photo) {
                        echo "file $photo not found ";
                        $newSheet->setCellValueByColumnAndRow(
                            0,
                            $newSheetCounter,
                            $sku
                        );
                        $newSheet->setCellValueByColumnAndRow(
                            1,
                            $newSheetCounter,
                            $newSheet->getCellByColumnAndRow(1, $newSheetCounter)->getValue() . ' ' . $fileName
                        );
                        $newSheetCounter++;
                    }
                }
                echo PHP_EOL;
            } else {
                echo "No photos in the excel file" . PHP_EOL;
            }
            $rowIndex++;
        }

        $this->saveFile($newFile);

    }

} 