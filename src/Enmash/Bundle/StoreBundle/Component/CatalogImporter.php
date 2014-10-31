<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/30/14
 * Time: 9:35 AM
 */

namespace Enmash\Bundle\StoreBundle\Component;

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

class CatalogImporter {

    const PATH = 'web/catalog/';
    const PATH_PHOTO = 'photo/';
    const FILENAME = 'catalog.ods';
    const GOODS_LIST_NAME = 'goods';
    const MANUFACTURERS_LIST_NAME = 'manufacturers';
    const TREE_LEVEL_1_LIST_NAME = 'TreeLevel1';
    const TREE_LEVEL_2_LIST_NAME = 'TreeLevel2';
    const TREE_LEVEL_3_LIST_NAME = 'TreeLevel3';
    const TREE_FULL_LIST = 'Tree';

    //cell coordinates
    const MANUFACTURERS_NAME_COLUMN = 0;
    const MANUFACTURERS_SITE_COLUMN = 1;

    const PRODUCT_CODE_COLUMN = 0;
    const PRODUCT_CATEGORY_COLUMN = 1;
    const PRODUCT_ACRONYM_COLUMN = 2;
    const PRODUCT_MAN_SKU = 3;
    const PRODUCT_MAN = 4;
    const PRODUCT_PHOTO = 7;

    const TREE_LEVEL_1_CATEGORY_NAME_COLUMN = 0;
    const TREE_SUBLEVEL_CATEGORY_NAME_COLUMN = 1;
    const TREE_SUBLEVEL_PARENT_CATEGORY_NAME_COLUMN = 0;

    /* @var $em \Doctrine\ORM\EntityManager */
    private $em;
    private $container;
    /* @var $kernel \AppKernel */
    private $kernel;

    public function __construct($em, $container, $kernel) {
        $this->em = $em;
        $this->container = $container;
        $this->kernel = $kernel;
    }

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

    public function importGoods(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $file->setActiveSheetIndexByName(self::GOODS_LIST_NAME);
        $sheet = $file->getActiveSheet();

        $rowIndex = 2;
        $goodsRepository = $this->em->getRepository('EnmashStoreBundle:Product');

        while ($sheet->cellExistsByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)) {

            $productCode = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_CODE_COLUMN, $rowIndex)
                ->getValue();

            //product sku | code
            $product = $goodsRepository->findOneBySku($productCode);
            if (!$product) {
                $product = new Product();
                $product->setSku($productCode);
            }

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

            //product acronym
            $acronym = $sheet
                ->getCellByColumnAndRow(self::PRODUCT_ACRONYM_COLUMN, $rowIndex)
                ->getValue();
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

            if (count($photosArray)) {
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
            $rowIndex++;

        }

    }

    protected function getPhoto($fileName) {
        $fileName = trim($fileName);

        if (file_exists(self::PATH . self::PATH_PHOTO . $fileName)) {
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

            $app = $this->getConsoleApp();
            $error = $this->runCommand($app, $fileName);

            if ($error !== 0) {
                throw new \Exception('Image ' . $fileName . ' was not added');
            }

            return $this->getPhoto($fileName);

        }

        return null;

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

    public function importCategories(\PHPExcel $file = null) {
        if (!$file) {
            $file = $this->getFile();
        }

        $this->importFirstLevelTree($file);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_2_LIST_NAME);
        $this->importSubLevelCategories($file, self::TREE_LEVEL_3_LIST_NAME);

    }

    protected function getFile() {
        /* @var $phpExcelObject \PHPExcel */
        try {
            $phpExcelObject = $this
                ->container
                ->get('phpexcel')
                ->createPHPExcelObject(self::PATH . self::FILENAME);
        } catch (\Exception $ex) {
            return null;
        }

        return $phpExcelObject;

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
            /* @var $category \Enmash\Bundle\StoreBundle\Entity\Category */
            $category = $categoriesRepository->findByName($categoryTitle);
            if (!$category) {
                $category = new Category();
                $category->setName($categoryTitle);
                $this->em->persist($category);
            }
            $rowIndex++;
        }
        $this->em->flush();
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

            $parentCategoryTitle = $sheet
                ->getCellByColumnAndRow(self::TREE_SUBLEVEL_PARENT_CATEGORY_NAME_COLUMN, $rowIndex)
                ->getValue();
            $parentCategory = $categoryRepository
                ->findOneByName($parentCategoryTitle);
            if (!$parentCategory) {
                throw new NotFoundHttpException('Parent category for ' . $categoryTitle . ' not found');
            }

            $category = $categoryRepository
                ->findOneByName($categoryTitle);
            if (!$category) {
                $category = new Category();
                $category->setName($categoryTitle);
            }
            $category->setParentCategory($parentCategory);
            $this->em->persist($category);

            $rowIndex++;
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

    protected function getConsoleApp() {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);
        return $app;
    }

    protected function runCommand($app, $filename) {
        $options = array(
            'command'   => 'sonata:media:add'
        );
        $options['providerName'] = 'sonata.media.provider.image';
        $options['context'] = 'productimage';
        $options['binaryContent'] = self::PATH . self::PATH_PHOTO . $filename;

        $input = new ArrayInput($options);
        $error = $app->run($input, null);

        return $error;
    }

} 