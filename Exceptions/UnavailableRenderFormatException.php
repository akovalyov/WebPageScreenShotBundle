<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

/**
 * Description of UnavailableRenderFormatException
 *
 * @author baptiste
 */
class UnavailableRenderFormatException extends \Exception {
    
    public function __construct($format)
    {
        parent::__construct(sprintf("%s format isn't available", $format), 0, null);
    }
}

?>