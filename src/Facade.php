<?php

namespace Dalab\MailManagement;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'MailManagement';
    }
}
