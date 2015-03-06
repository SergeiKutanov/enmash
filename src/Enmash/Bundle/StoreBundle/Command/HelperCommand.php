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
use Enmash\Bundle\StoreBundle\Entity\Category;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelperCommand extends ContainerAwareCommand {

    const PHOTO_DIR_PATH = '/web/catalog/photo/';
    const PHOTO_RAW_DIR_PATH = '/web/catalog/photo/raw/';
    const PHOTO_STAMP_PATH = '/web/catalog/photo/raw/stamp/stamp.png';
    const OPTION_HELPER = 'fix-extensions';
    const OPTION_MARK_IMAGES = 'mark-images';
    const OPTION_REMOVE_ALL_IMAGES = 'clear-images';

    protected function configure() {
        $this
            ->setName('helper:run')
            ->setDescription('Misc helper commands')
            ->addOption(
                'mode',
                'm',
                InputArgument::OPTIONAL,
                'Export type: ' . self::OPTION_HELPER
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $helperName = $input
            ->getOption('mode');

        switch ($helperName) {
            case self::OPTION_HELPER:
                $this->runFixPhotoExtensions();
                break;
            case self::OPTION_MARK_IMAGES:
                $this->markImages();
                break;
            case self::OPTION_REMOVE_ALL_IMAGES:
                $this->clearImages();
                break;
        }

        $output->writeln('Done');

    }

    private function runFixPhotoExtensions()
    {
        $path = realpath('') . self::PHOTO_DIR_PATH;
        $files = scandir($path);
        $files = array_slice($files, 2);
        foreach ($files as $file) {
            $ext = pathinfo($path . $file, PATHINFO_EXTENSION);
            $newFileName = pathinfo($path . $file, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($path . $file, PATHINFO_EXTENSION));
            rename($path . $file, $path . $newFileName);
        }
    }

    private function markImages()
    {
        $path = realpath('') . self::PHOTO_RAW_DIR_PATH;
        $files = scandir($path);
        $files = array_slice($files, 2);

        $stamp = $this->getStampImage();
        $marginRight = 10;
        $marginBottom = 10;
        $stampX = imagesx($stamp);
        $stampY = imagesy($stamp);

        foreach ($files as $index => $file) {
            if (!is_file($path . $file)) continue;
            echo $index . ": $path $file" . PHP_EOL;
            $fileExtension = pathinfo($path . $file, PATHINFO_EXTENSION);
            $image = null;
            if ($fileExtension == 'png') {
                $image = imagecreatefrompng($path . $file);
            } else {
                try {
                    $image = imagecreatefromjpeg($path . $file);
                } catch (\Exception $ex) {
                    echo $ex->getMessage() . PHP_EOL;
                }
            }

            if (!$image) {
                echo "Could not create image object for file: $file" . PHP_EOL;
                continue;
            }


            imagecopy(
                $image,
                $stamp,
                imagesx($image) - $stampX - $marginRight,
                imagesy($image) - $stampY - $marginBottom,
                0, 0,
                $stampX,
                $stampY
            );

            if ($fileExtension == 'png') {
                imagepng($image, $path . $file);
            } else {
                imagejpeg($image, $path . $file);
            }
            imagedestroy($image);

        }

    }

    private function getStampImage()
    {
        $stamp = imagecreatefrompng(realpath('') . self::PHOTO_STAMP_PATH);
        return $stamp;
    }

    private function clearImages($category = null)
    {
        /* @var $gm \Sonata\MediaBundle\Entity\GalleryManager */
        $gm = $this->getContainer()->get('sonata.media.manager.gallery');

        /* @var $mm MediaManager */
        $mm = $this->getContainer()->get('sonata.media.manager.media');

        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();

        if ($category) {
            /** @var $category Category*/
            $category = $em
                ->getRepository('EnmashStoreBundle:Category')
                ->find($category);
            $allProducts = $category->getAllProducts();
            $products = array();
            foreach ($allProducts as $product) {
                if ($product->getProductImages()) {
                    $products[] = $product;
                }
            }
        } else {
            $products = $em
                ->getRepository('EnmashStoreBundle:Product')
                ->createQueryBuilder('p')
                ->where('p.productImages IS NOT NULL')
                ->getQuery()
                ->execute();
        }

        foreach ($products as $index => $product) {
            /* @var $product \Enmash\Bundle\StoreBundle\Entity\Product */
            $gallery = $product->getProductImages();
            echo $index . ": " . $product->getId() . PHP_EOL;
            echo 'Deleting gallery: ' . $gallery->getId() . PHP_EOL;

            $product->setProductImages();

            $gm->delete($gallery);
            foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
                /* @var $galleryHasMedia \Application\Sonata\MediaBundle\Entity\GalleryHasMedia */
                $media = $galleryHasMedia->getMedia();
                echo 'Deleting media: ' . $media->getId() . PHP_EOL;
                try {
                    $galleriesMediaIsIn = $em
                        ->getRepository('ApplicationSonataMediaBundle:GalleryHasMedia')
                        ->findBy(
                            array(
                                'media' => $media->getId()
                            )
                        );
                    if (!count($galleriesMediaIsIn)) {
                        $mm->delete($media);
                        echo 'Media: ' . $media->getId() . " removed" . PHP_EOL;
                    }
                } catch (\Exception $ex) {
                    echo $ex->getMessage() . PHP_EOL;
                    echo 'Media is not removed.' . PHP_EOL;
                }
            }

        }

    }

}