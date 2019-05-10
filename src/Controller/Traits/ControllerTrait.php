<?php


namespace App\Controller\Traits;

use Psr\Container\ContainerInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @property ContainerInterface $container
 */
trait ControllerTrait
{
    /**
     * Get Knp paginator bundle
     *
     * @return PaginatorInterface
     */
    public function getKnpPaginator(): PaginatorInterface
    {
        if (!$this->container->has('knp_paginator')) {
            throw new \LogicException('The KnpPaginatorBundle is not registered in your application.');
        }

        return $this->container->get('knp_paginator');
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function trans(string $id, array $parameters = array(), ?string $domain = null): ?string
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null): ?string
    {
        return $this->container->get('translator')->transChoice($id, $number, $parameters, $domain);
    }

    /**
     * Validate the given date.
     *
     * @param $date
     * @param bool $empty_allowed
     * @return bool
     */
    protected function validateDate( $date, $empty_allowed = true ) {

        if ( empty( $date ) ) {
            return $empty_allowed;
        }

        if ( preg_match( '/(0[1-9]|1[0-9]|3[01])\/(0[1-9]|1[012])\/(2[0-9][0-9][0-9]|1[6-9][0-9][0-9])/', $date ) !== 1 ) {
            return false;
        }

        $split = explode( '/', $date );

        return checkdate( $split[1], $split[0], $split[2] );
    }
}