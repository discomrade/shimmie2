<?php

declare(strict_types=1);

namespace Shimmie2;

use MicroHTML\HTMLElement;

use function MicroHTML\rawHTML;

class Yotsuba_BIndexTheme extends IndexTheme
{
    /**
     * @param Image[] $images
     */
    public function display_page(Page $page, array $images): void
    {
        $this->display_shortwiki($page);

        $this->display_page_header($page, $images);

        $nav = $this->build_navigation($this->page_number, $this->total_pages, $this->search_terms);
        $page->add_block(new Block("Navigation", $nav, "left", 0));

        if (count($images) > 0) {
            $this->display_page_images($page, $images);
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

        $h_prev = ($page_number <= 1) ? "Prev" : '<a href="'.search_link($search_terms, $prev).'">Prev</a>';
        $h_index = "<a href='".make_link()."'>Index</a>";
        $h_next = ($page_number >= $total_pages) ? "Next" : '<a href="'.search_link($search_terms, $next).'">Next</a>';

        $h_search_string = html_escape(Tag::implode($search_terms));
        $h_search_link = search_link();
        $h_search = "
			<form action='$h_search_link' method='GET'>
				<input type='search' name='search' value='$h_search_string' class='search_field autocomplete_tags' />
				<input type='submit' value='Search'>
				<input type='hidden' name='q' value='post/list'>
				<input type='submit' value='Find' style='display: none;' />
			</form>
		";

        return rawHTML($h_prev.' | '.$h_index.' | '.$h_next.'<br>'.$h_search);
    }
}
