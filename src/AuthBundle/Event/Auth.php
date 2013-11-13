<?php

namespace AuthBundle\Event;

class Auth
{
    /**
     * Is triggered only when filled login form and the data are correct.
    **/
    const IS_LOGGED = 'is_logged';

    /**
     * Is triggered when user has been registration succesfully.
    **/
    const IS_REGISTERED = 'is_registered';

    /**
     * Is triggered when user log out.
    */
    const IS_LOGGED_OUT = 'is_logged_out';
}
