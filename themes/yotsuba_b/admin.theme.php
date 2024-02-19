<?php

declare(strict_types=1);

namespace Shimmie2;

class Yotsuba_BAdminPageTheme extends AdminPageTheme
{
    public function display_page(): void
    {
        Ctx::$page->set_layout("no-left");
        parent::display_page();
    }
}
