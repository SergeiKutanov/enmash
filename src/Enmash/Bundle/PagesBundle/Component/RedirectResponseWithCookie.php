<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 13.01.15
 * Time: 11:10
 */

namespace Enmash\Bundle\PagesBundle\Component;

use Symfony\Component\HttpFoundation\Cookie;

class RedirectResponseWithCookie extends \Symfony\Component\HttpFoundation\RedirectResponse {

    public function __construct($url, $status = 302, $cookies = array ())
    {
        parent::__construct($url, $status);

        foreach ($cookies as $cookie)
        {
            if (!$cookie instanceof Cookie)
            {
                throw new \InvalidArgumentException(sprintf('Third parameter is not a valid Cookie object.'));
            }
            $this->headers->setCookie($cookie);
        }
    }

}