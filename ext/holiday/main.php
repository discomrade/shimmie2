<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{LINK,SCRIPT};

class Holiday extends Extension
{
    public function onInitExt(InitExtEvent $event): void
    {
        global $config;
        $config->set_default_bool("holiday_aprilfools", false);
    }

    public function onSetupBuilding(SetupBuildingEvent $event): void
    {
        $sb = $event->panel->create_new_block("Holiday Theme");
        $sb->add_bool_option("holiday_aprilfools", "Enable Alunya neko on April Fools & May Day");
    }

    public function onPageRequest(PageRequestEvent $event): void
    {
        global $config, $page;
        if ((date('d/m') == '01/04' || date('d/m') == '01/05') && $config->get_bool("holiday_aprilfools")) {
            $page->add_html_header(SCRIPT([
                'src' => get_base_href() . '/ext/holiday/javascript/jneko.js',
            ]));
            $page->add_html_header(LINK([
                'rel' => 'stylesheet',
                'href' => get_base_href() . '/ext/holiday/stylesheets/jneko.css',
                'type' => 'text/css'
            ]));

        }
    }
}
