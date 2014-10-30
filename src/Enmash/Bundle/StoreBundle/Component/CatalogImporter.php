<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/30/14
 * Time: 9:35 AM
 */

namespace Enmash\Bundle\StoreBundle\Component;


use Enmash\Bundle\StoreBundle\Entity\Category;
use Enmash\Bundle\StoreBundle\Entity\Manufacturer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogImporter {

    const PATH = 'web/catalog/';
    const FILENAME = 'catalog.ods';
    const GOODS_LIST_NAME = 'goods';
    const MANUFACTURERS_LIST_NAME = 'manufacturers';
    const TREE_LEVEL_1_LIST_NAME = 'TreeLevel1';
    const TREE_LEVEL_2_LIST_NAME = 'TreeLevel2';
    const TREE_LEVEL_3_LIST_NAME = 'TreeLevel3';

    //cell coordinates
    const MANUFACTURERS_NAME_COLUMN = 0;
    const MANUFACTURERS_SITE_COLUMN = 1;

    const PRODUCT_CODE_COLUMN = 0;
    const PRODUCT_ACRONYM_COLUMN = 2;
    const PRODUCT_MAN_SKU = 3;
    const PRODUCT_MAN = 4;

    const TREE_LEVEL_1_CATEGORY_NAME_COLUMN = 0;
    const TREE_SUBLEVEL_CATEGORY_NAME_COLUMN = 1;
    const TREE_SUBLEVEL_PARENT_CATEGORY_NAME_COLUMN = 0;

    /* @var $em \Doctrine\ORM\EntityManager */
    private $em;
    private $container;

    public function __construct($em, $container) {
        $this->em = $em;
        $this->container = $container;
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

} 