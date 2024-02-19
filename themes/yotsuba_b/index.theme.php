<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{A,INPUT,P,emptyHTML,joinHTML};
use function MicroHTML\DIV;

use MicroHTML\HTMLElement;

class Yotsuba_BIndexTheme extends IndexTheme
{
    /**
     * @param Image[] $images
     */
    public function display_page(array $images): void
    {
        $this->display_shortwiki();

        $this->display_page_header($images);

        $nav = $this->build_navigation($this->page_number, $this->total_pages, $this->search_terms);
        Ctx::$page->add_block(new Block("Search", $nav, "left", 0));

        if (count($images) > 0) {
            $this->display_page_images($images);
        } else {
            throw new PostNotFound("No posts were found to match the search criteria");
        }
    }

    /**
     * @param string[] $search_terms
     */
    protected function build_navigation(int $page_number, int $total_pages, array $search_terms): HTMLElement
    {
        $prev = $page_number - 1;
        $next = $page_number + 1;

        $pin = DIV(
            ["id" => "navigation-pin"],
            DIV(
                A(["href" => "/help/search"], "Search Help")
            ),
            P(),
            joinHTML(" | ", [
                ($page_number <= 1) ? "Prev" : A(["href" => search_link($search_terms, $prev), "id" => "prevlink"], "Prev"),
                ($page_number >= $total_pages) ? "Next" : A(["href" => search_link($search_terms, $next), "id" => "nextlink"], "Next")
            ])
        );


        $search = SHM_FORM(
            action: search_link(),
            method: 'GET',
            children: [
                INPUT([
                    "name" => 'search',
                    "placeholder" => "red_star",
                    "type" => 'text',
                    "value" => Tag::implode($search_terms),
                    "class" => 'autocomplete_tags',
                ]),
                INPUT([
                    "type" => 'submit',
                    "value" => 'Search',
                ])
            ]
        );

        return emptyHTML($search, P(), $pin);
    }

    /**
     * @param Image[] $images
     */
    protected function build_table(array $images, ?string $query): HTMLElement
    {
        $table = DIV(["class" => "shm-image-list", "data-query" => $query]);
        foreach ($images as $image) {
            $table->appendChild($this->build_thumb($image));
        }
        return $table;
    }
}
