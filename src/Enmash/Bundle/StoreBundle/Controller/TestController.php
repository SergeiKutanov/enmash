<?php

namespace Enmash\Bundle\StoreBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Sonata\MediaBundle\Entity\GalleryManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * @Route("/test")
     */
    public function runTestAction() {

        /* @var $mm MediaManager */
        $mm = $this->container->get('sonata.media.manager.media');

        /* @var $galleryManager GalleryManager */
        $galleryManager = $this->container->get('sonata.media.manager.gallery');

        $em = $this->getDoctrine()->getManager();
        /* @var $product Product */

        $media = new Media();
        $filePath = realpath('') . '/catalog/photo/С0108.jpg';
        if (is_file($filePath)) {
            $media->setBinaryContent($filePath);
            $media->setContext('productimage');
            $media->setProviderName('sonata.media.provider.image');
            $mm->save($media);

            $gallery = new Gallery();
            $gallery->setName('test');
            $gallery->setContext('productimage');
            $gallery->setDefaultFormat('productimage_small');
            $gallery->setEnabled(true);

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setGallery($gallery);

            $gallery->addGalleryHasMedias(
                $galleryHasMedia
            );
            $galleryManager->save($gallery);
        }
//        $media->setBinaryContent()

        return new Response();
    }
}
