<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{A,DIV,INPUT,P,emptyHTML};

use MicroHTML\HTMLElement;

class Yotsuba_BViewPostTheme extends ViewPostTheme
{
    /**
     * @param HTMLElement[] $editor_parts
     */
    public function display_page(Image $image, array $editor_parts): void
    {
        Ctx::$page->set_heading($image->get_tag_list());
        Ctx::$page->add_block(new Block("Search", $this->build_navigation($image), "left", 0));
        Ctx::$page->add_block(new Block("Information", $this->build_stats($image), "left", 15));
        Ctx::$page->add_block(new Block(null, $this->build_info($image, $editor_parts), "main", 15));
    }

    protected function build_navigation(Image $image): HTMLElement
    {
        $pin = DIV(
            ["id" => "navigation-pin"],
            A(["href" => "/help/search"], "Search Help")
        );

        $search = SHM_FORM(
            action: search_link(),
            method: 'GET',
            children: [
                INPUT([
                    "name" => 'search',
                    "placeholder" => "red_star",
                    "type" => 'text',
                    "class" => 'autocomplete_tags',
                ]),
                INPUT([
                    "type" => 'submit',
                    "value" => 'Search',
                ]),
            ]
        );

        return emptyHTML($search, P(), $pin);
    }
}
