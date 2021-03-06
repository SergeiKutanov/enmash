<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            //custom bundles
            //enmash main bundle
            new Enmash\Bundle\PagesBundle\EnmashPagesBundle(),

            //sonata bundles
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new \Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new \Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new \Sonata\AdminBundle\SonataAdminBundle(),
            new Enmash\Bundle\UserControlBundle\EnmashUserControlBundle(),
            new Enmash\Bundle\StoreBundle\EnmashStoreBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new \Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \Sonata\FormatterBundle\SonataFormatterBundle(),
            new \WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            //php excel helpers
            new \Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new \CoopTilleuls\Bundle\CKEditorSonataMediaBundle\CoopTilleulsCKEditorSonataMediaBundle(),

            new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),

            //seo staff
            new Sonata\SeoBundle\SonataSeoBundle(),

            //image processing
            new Liip\ImagineBundle\LiipImagineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
