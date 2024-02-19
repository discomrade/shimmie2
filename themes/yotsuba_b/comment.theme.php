<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{A, DIV, P, SPAN, emptyHTML, /*};*/ rawHTML};

use MicroHTML\HTMLElement;

class Yotsuba_BCommentListTheme extends CommentListTheme
{
    public function display_recent_comments(array $comments): void
    {
        $html = emptyHTML();
        foreach ($comments as $comment) {
            $html->appendChild($this->comment_to_html($comment, true));
        }
        $html->appendChild(A(["class" => "more", "href" => make_link("comment/list")], "Full List"));
        Ctx::$page->add_block(new Block("Comments", $html, "left", 70, "comment-list-recent"));
    }

    /**
     * @param $comment $trim
     */

    protected function comment_to_html(Comment $comment, bool $trim = false): HTMLElement
    {
        global $config, $user;

        $tfe = send_event(new TextFormattingEvent($comment->comment));

        $i_uid = $comment->owner_id;
        $h_name = html_escape($comment->owner_name);
        //$h_poster_ip = html_escape($comment->poster_ip);
        if ($trim) {
            $text = strval(preg_replace("/( *(\n) *)+/", "\n", $tfe->stripped));
            $h_comment = truncate($text, 80);
            $h_comment = strval(preg_replace('/(^|\n)(&gt;(?!&gt;).*)/', '${1}<span class="greentext">${2}</span>', $h_comment));
        } else {
            $h_comment = $tfe->formatted;
        }

        // handles discrepency in comment page and homepage
        $h_comment = str_replace("<br>", "", $h_comment);
        $h_comment = str_replace("\n", "<br>", $h_comment);
        $i_comment_id = $comment->comment_id;
        $i_image_id = $comment->image_id;

        if ($trim) {
            // can't nest <a> tags
            $h_userlink = SPAN(["class" => "username"], $h_name);
        } else {
            $h_userlink = A(["class=" => "username", "href" => make_link('user/'.$h_name)], $h_name);
        }
        if ($trim) {
            $html = A(
                ["class" => "comment-trim-link", "href" => make_link("post/view/$i_image_id", null, "c$i_comment_id")],
                DIV(
                    ["id" => "c$i_comment_id", "class" => "comment"],
                    $h_userlink,
                    ": ",
                    rawHTML($h_comment)
                )
            );
        } else {
            $h_userlink = A(["class" => "username", "href" => make_link("user/$h_name")], $h_name);
            $h_date = $comment->posted;
            $h_del = " ";
            if (Ctx::$user->can(CommentPermission::DELETE_COMMENT)) {
                $comment_preview = substr(html_unescape($tfe->stripped), 0, 50);
                $j_delete_confirm_message = json_encode("Delete comment by {$comment->owner_name}:\n$comment_preview");
                $h_delete_script = html_escape("return confirm($j_delete_confirm_message);");
                $h_delete_link = make_link("comment/delete/$i_comment_id/$i_image_id");
                $h_del = emptyHTML(
                    " - [",
                    A(["onclick" => "$h_delete_script", "href" => "$h_delete_link"], "Delete"),
                    "] "
                );
            }
            $h_reply = emptyHTML(
                "[",
                A(["href" => "javascript: replyTo($i_image_id, $i_comment_id, \"$i_comment_id\")"], "Reply"),
                "]"
            );
            $html = DIV(
                ["id" => "c$i_comment_id", "class" => "comment"],
                $h_userlink,
                $h_del,
                $h_date,
                " No.",
                $i_comment_id,
                " ",
                $h_reply,
                P(rawHTML($h_comment))
            );
        }
        return $html;
    }
}
