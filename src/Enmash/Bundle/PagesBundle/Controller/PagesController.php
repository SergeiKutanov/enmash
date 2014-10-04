<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/4/14
 * Time: 2:30 PM
 */

namespace Enmash\Bundle\PagesBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PagesController extends Controller{

    /**
     * @Route("/", name="index-page")
     * @Method("GET")
     * @Template("EnmashPagesBundle:Pages:index.html.twig")
     */
    public function indexAction() {
        return array();
    }

} 