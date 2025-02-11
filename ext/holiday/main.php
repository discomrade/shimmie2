<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{LINK,SCRIPT};

final class Holiday extends Extension
{
    public const KEY = "holiday";

    public function onPageRequest(PageRequestEvent $event): void
    {
        if ((date('m/d') == '04/01' || date('m/d') == '05/01') && Ctx::$config->get(HolidayConfig::ALUNYA_NEKO)) {
            Ctx::$page->add_html_header(SCRIPT([
                'src' => Url::base() . '/ext/holiday/javascript/jneko.js',
            ]));
            Ctx::$page->add_html_header(LINK([
                'rel' => 'stylesheet',
                'href' => Url::base() . '/ext/holiday/stylesheets/jneko.css',
                'type' => 'text/css'
            ]));
        }

        if (date('m/d') == '04/01' && Ctx::$config->get(HolidayConfig::APRIL_FOOLS)) {
            Ctx::$page->add_html_header(LINK([
                'rel' => 'stylesheet',
                'href' => Url::base() . '/ext/holiday/stylesheets/aprilfools.css',
                'type' => 'text/css'
            ]));
        }
    }
}
