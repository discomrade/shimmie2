<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{A, DIV, INPUT, TD, TH, TR, emptyHTML, joinHTML};

use MicroHTML\HTMLElement;

class PostSourceTheme extends Themelet
{
    public function mss_html(string $terms): HTMLElement
    {
        return SHM_SIMPLE_FORM(
            make_link("tag_edit/mass_source_set"),
            INPUT(["type" => "hidden", "name" => "tags", "value" => $terms]),
            INPUT(["type" => "text", "name" => "source", "value" => '']),
            INPUT(["type" => "submit", "value" => "Set Source For All", "onclick" => "return confirm(\"This will mass-edit all sources on the page.\\nAre you sure you want to do this?\")"])
        );
    }

    public function get_source_editor_html(Image $image): HTMLElement
    {
        return SHM_POST_INFO(
            "Source",
            DIV(
                self::format_source($image->get_source())
            ),
            Ctx::$user->can(PostSourcePermission::EDIT_IMAGE_SOURCE) ? INPUT(["type" => "text", "name" => "source", "value" => $image->get_source()]) : null,
            link: SourceHistoryInfo::is_enabled() ? make_link("source_history/{$image->id}") : null,
        );
    }

    public static function format_source(?string $source = null): HTMLElement
    {
        if (!empty($source)) {
            $words = [];
            foreach (explode(' ', $source) as $word) {
                if (str_starts_with($word, "http") && filter_var($word, FILTER_VALIDATE_URL)) {
                    $words[] = A(["href" => $word], $word);
                } else {
                    $words[] = emptyHTML($word);
                }
            }
            return joinHTML(' ', $words);
        }
        return emptyHTML("Unknown");
    }

    public function get_upload_common_html(): HTMLElement
    {
        return TR(
            TH(["width" => "20"], "Common Source"),
            TD(["colspan" => "6"], INPUT(["name" => "source", "type" => "text", "placeholder" => "https://..."]))
        );
    }

    public function get_upload_specific_html(string $suffix): HTMLElement
    {
        return TD(
            INPUT([
                "type" => "text",
                "name" => "source{$suffix}",
                "value" => ($suffix == 0) ? @$_GET['source'] : null,
            ])
        );
    }
}
