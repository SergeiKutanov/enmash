<?php

namespace Enmash\Bundle\StoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StoreAdminController extends CRUDController {

    public function apiParametersForCategoryAction(Request $request) {
        $categoryId = $request->get('category_id', null);
        if (is_int((integer)$categoryId)) {
            $em = $this->getDoctrine()->getManager();
            /* @var $category \Enmash\Bundle\StoreBundle\Entity\Category */
            $category = $em
                ->getRepository('EnmashStoreBundle:Category')
                ->find($categoryId);
            if (!$category) {
                throw new NotFoundHttpException('Category not found');
            }

            $serializer = $this->container->get('jms_serializer');
            $data = $serializer->serialize($category->getParameters(), 'json');

            return new Response($data);
        }

        return new Response(array());
    }

}
