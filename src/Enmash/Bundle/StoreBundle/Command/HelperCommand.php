<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 27.01.15
 * Time: 10:42
 */

namespace Enmash\Bundle\StoreBundle\Command;


use Enmash\Bundle\StoreBundle\Entity\EnbVmProduct;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelperCommand extends ContainerAwareCommand {

    const PHOTO_HOST = "http://enmash.ru/product/";
    const CATALOG_PATH = "/web/catalog/photo/";

    private $em;

    protected function configure() {
        $this
            ->setName('helper:restore-photos')
            ->setDescription('Restore photos from old site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Import Started");

        $this->em = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $oldProducts = $this->findOldProducts();
    }

    private function findOldProducts()
    {
        $oldProducts = $this
            ->em
            ->getRepository('EnmashStoreBundle:EnbVmProduct')
            ->findProductsWithPictures();

        $counter = 0;
        foreach ($oldProducts as $oldProduct) {
            $counter++;
            /* @var $oldProduct \Enmash\Bundle\StoreBundle\Entity\EnbVmProduct */
            if ($oldProduct->getProductSku()) {
                $newProduct = $this
                    ->em
                    ->getRepository(
                        'EnmashStoreBundle:Product'
                    )
                    ->findOneBy(
                        array(
                            'sku'   => $oldProduct->getProductSku()
                        )
                    );
                if (!$newProduct) continue;
                $this->copyPicture($newProduct, $oldProduct);
                echo $counter . ' -- ' . $newProduct->getSku() . ' image copied' . PHP_EOL;
            }
        }
    }

    private function copyPicture(Product $newProduct, EnbVmProduct $oldProduct)
    {
        $filePath = $oldProduct->getProductFullImage();
        $image = @file_get_contents(self::PHOTO_HOST . $filePath);
        if (!$image) return;
        $path = realpath('') . self::CATALOG_PATH . $newProduct->getSku() . '.jpg';
        file_put_contents(
            $path,
            $image
        );
    }


}