<?php

declare(strict_types=1);

namespace Shimmie2;

class Filter extends Extension
{
    /** @var FilterTheme */
    protected Themelet $theme;

    public function onInitExt(InitExtEvent $event): void
    {
        global $config;
        $config->set_default_string(FilterConfig::TAGS, "spoilers\nguro\nscat\nfurry -rating:s\n");
    }

    public function onPageRequest(PageRequestEvent $event): void
    {
        global $page;
        $this->theme->addFilterBox();
    }

    public function onSetupBuilding(SetupBuildingEvent $event): void
    {
        $event->panel->add_config_group(new FilterConfig());
    }

    public function onInitUserConfig(InitUserConfigEvent $event): void
    {
        global $config;
        $event->user_config->set_default_string(FilterUserConfig::TAGS, $config->get_string(FilterConfig::TAGS));
    }

    public function onUserOptionsBuilding(UserOptionsBuildingEvent $event): void
    {
        $event->panel->add_config_group(new FilterUserConfig());
    }
}
