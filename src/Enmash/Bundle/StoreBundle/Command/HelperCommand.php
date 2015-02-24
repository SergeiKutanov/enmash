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

class HelperCommand extends ContainerAwareCommand {

    const PHOTO_DIR_PATH = '/web/catalog/photo/';
    const PHOTO_RAW_DIR_PATH = '/web/catalog/photo/raw/';
    const PHOTO_STAMP_PATH = '/web/catalog/photo/raw/stamp/stamp.png';
    const OPTION_HELPER = 'fix-extensions';
    const OPTION_MARK_IMAGES = 'mark-images';

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

        foreach ($files as $file) {
            if (!is_file($path . $file)) continue;

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

}