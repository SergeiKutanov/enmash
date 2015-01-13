<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 13.01.15
 * Time: 10:08
 */

namespace Enmash\Bundle\PagesBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocationChangeEventListener {

    const LOCATION_COOKIE_NAME = 'userlocation';

    public function onRequestMade(GetResponseEvent $event) {
        $request = $event->getRequest();

        $location = $request
            ->cookies
            ->get(
                self::LOCATION_COOKIE_NAME
            );

        if ($location) {

        }

        $request
            ->attributes
            ->set(
                self::LOCATION_COOKIE_NAME,
                $location
            );
    }

    public function onResponseMade(FilterResponseEvent $event) {

        if (!function_exists('geoip_record_by_name')) {
            return;
        }

        $request = $event->getRequest();
        $location = $this->checkCookie($request->cookies);
        if (!$location) {
            $location = $this->findUserLocationByIp($request->getClientIp());
        }

        if (!$location) return;

        $cookie = $this->createCookie($location);
        $event
            ->getResponse()
            ->headers
            ->setCookie($cookie);

    }

    private function checkCookie(ParameterBag $cookie) {

        $location = $cookie->get(self::LOCATION_COOKIE_NAME);
        return $location;

    }

    private function findUserLocationByIp($ip) {
        $ip = '8.8.8.8';
        $record = (geoip_record_by_name($ip));

        if (!$record) return null;
        if (!array_key_exists('city', $record)) return null;

        return $record['city'];
    }

    private function createCookie($location) {

        $expDate = new \DateTime();
        $expDate->add(new \DateInterval('P1D'));

        $cookie = new Cookie(
            self::LOCATION_COOKIE_NAME,
            $location,
            $expDate
        );

        return $cookie;
    }

}