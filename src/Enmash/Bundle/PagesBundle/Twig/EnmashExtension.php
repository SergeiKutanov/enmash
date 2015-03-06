<?php

namespace Enmash\Bundle\PagesBundle\Twig;

use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\DependencyInjection\Container;

class EnmashExtension extends \Twig_Extension {

    /* @var $container Container */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'media_relative_path',
                array(
                    $this,
                    'mediaRelativePathFilter'
                )
            )
        );
    }

    public function mediaRelativePathFilter(Media $media)
    {
        /* @var $provider ImageProvider */
        $provider = $this->container->get($media->getProviderName());
        return 'uploads/media/' . $provider->generatePath($media) . '/thumb_' . $media->getId() . '_' . $provider->getFormatName($media, 'big') . '.' . $media->getExtension();

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'enmash_extension';
    }
}