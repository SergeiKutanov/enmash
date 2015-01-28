<?php

namespace Enmash\Bundle\PagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    protected function setSeoData(
        $title = null,
        $description = null,
        $keywords = null
    ) {
        /* @var $seoService \Sonata\SeoBundle\Seo\SeoPage */
        $seoService = $this
            ->get('sonata.seo.page');

        if ($title) {
            $seoService->addTitle($title);
        }
        if ($description) {
            $seoService->addMeta(
                'name',
                'description',
                $description
            );
        }
        if ($keywords) {
            $seoService->addMeta(
                'name',
                'keywords',
                $keywords
            );
        }
    }
}
