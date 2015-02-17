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
    const OPTION_HELPER = 'fix-extensions';

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

}