<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 30.01.15
 * Time: 9:44
 */

namespace Enmash\Bundle\StoreBundle\Component\Catalog;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class Catalog {

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
    const PRODUCT_NAME_COLUMN = 2;
    const PRODUCT_ACRONYM_COLUMN = 3;
    const PRODUCT_MAN_SKU = 4;
    const PRODUCT_MAN = 5;
    const PRODUCT_ANALOGS_COLUMN = 6;
    const PRODUCT_SIMILAR_COLUMN = 7;
    const PRODUCT_PHOTO = 8;
    const PRODUCT_DESCRIPTION_COLUMN = 9;
    const PRODUCT_CERTIFICATE_COLUMN = 10;


    const TREE_LEVEL_1_CATEGORY_NAME_COLUMN = 0;
    const TREE_SUBLEVEL_CATEGORY_NAME_COLUMN = 1;
    const TREE_SUBLEVEL_PARENT_CATEGORY_NAME_COLUMN = 0;

    /* @var $em \Doctrine\ORM\EntityManager */
    protected  $em;
    protected $container;
    /* @var $kernel \AppKernel */
    protected $kernel;

    public function __construct($em, $container, $kernel) {
        $this->em = $em;
        $this->container = $container;
        $this->kernel = $kernel;
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

    protected function getConsoleApp() {
        $app = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->kernel);
        $app->setAutoExit(false);
        return $app;
    }

    protected function runCommand($app, $filename) {
        $options = array(
            'command'   => 'sonata:media:add'
        );
        $options['providerName'] = 'sonata.media.provider.image';
        $options['context'] = 'productimage';
        $options['binaryContent'] =  self::PATH . self::PATH_PHOTO . $filename;
        $input = new ArrayInput($options);
        $error = $app->run($input, null);

        return $error;
    }
}