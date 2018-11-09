<?php

namespace Kgregorywd\MailManagement\Extensions;

use Menu;

class MenuBuilder
{
    public static function build()
    {
        Menu::make('sidebar', function ($menu){

            // First Level
            // Second Level

            $menu->add(trans("MailManagement::backend.menu.emails"), 'mailboxes')
                ->active('mailboxes/*')
                ->data('icon', 'th-list')
                ->data('category', 0)
                ->data('order', PHP_INT_MAX-2);
        });
    }
}
