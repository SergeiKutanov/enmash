<?php

namespace Enmash\Bundle\UserControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\BrowserKit\Request;

/**
 * @Route("/user")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/index", name="index")
     * @Method("GET")
     */
    public function privateIndexAction() {
        die("private index");
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request) {

        die('register');
    }

}
