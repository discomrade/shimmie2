<?php

declare(strict_types=1);

namespace Shimmie2;

class Yotsuba_BCommentListTheme extends CommentListTheme
{
    private bool $show_anon_id = false;
    private int $anon_id = 1;
    /** @var array<string,int> */
    private array $anon_map = [];

    /**
     * @param $comment $trim
     */

    protected function comment_to_html(Comment $comment, bool $trim = false): string
    {
        global $config, $user;

        $tfe = send_event(new TextFormattingEvent($comment->comment));

        $i_uid = $comment->owner_id;
        $h_name = html_escape($comment->owner_name);
        //$h_poster_ip = html_escape($comment->poster_ip);
        if ($trim) {
            $text = strval(preg_replace("/( *(\n) *)+/", "\n", $tfe->stripped));
            $h_comment = truncate($text, 80);
        } else {
            $h_comment = $tfe->formatted;
        }
        $h_comment = strval(preg_replace("/(^|>)(&gt;[^<\n]*)(<|\n|$)/", '${1}<span class=\'greentext\'>${2}</span>${3}', $h_comment));
        // handles discrepency in comment page and homepage
        $h_comment = str_replace("<br>", "", $h_comment);
        $h_comment = str_replace("\n", "<br>", $h_comment);
        $i_comment_id = $comment->comment_id;
        $i_image_id = $comment->image_id;

        if ($i_uid == $config->get_int("anon_id")) {
            $anoncode = "";
            $anoncode2 = "";
            if ($this->show_anon_id) {
                $anoncode = '<sup>'.$this->anon_id.'</sup>';
                if (!array_key_exists($comment->poster_ip, $this->anon_map)) {
                    $this->anon_map[$comment->poster_ip] = $this->anon_id;
                }
                #if($user->can(UserAbilities::VIEW_IP)) {
                #$style = " style='color: ".$this->get_anon_colour($comment->poster_ip).";'";
                if ($user->can(Permissions::VIEW_IP) || $config->get_bool("comment_samefags_public", false)) {
                    if ($this->anon_map[$comment->poster_ip] != $this->anon_id) {
                        $anoncode2 = '<sup>('.$this->anon_map[$comment->poster_ip].')</sup>';
                    }
                }
            }
            $h_userlink = "<span class='username'>" . $h_name . $anoncode . $anoncode2 . "</span>";
            $this->anon_id++;
        } else {
            if ($trim) {
                // can't nest <a> tags
                $h_userlink = '<span class="username">'.$h_name.'</span>';
            } else {
                $h_userlink = '<a class="username" href="'.make_link('user/'.$h_name).'">'.$h_name.'</a>';
            }
        }

        $hb = ($comment->owner_class == "hellbanned" ? "hb" : "");
        if ($trim) {
            $html = "
			<a class='comment-trim-link' href='".make_link("post/view/$i_image_id", null, "c$i_comment_id")."'>
			<div id=\"c$i_comment_id\" class=\"comment $hb\">
				$h_userlink: $h_comment
			</div></a>
			";
        } else {
            $h_userlink = "<a class='username' href='".make_link("user/$h_name")."'>$h_name</a>";
            $h_date = $comment->posted;
            $h_del = "";
            if ($user->can(Permissions::DELETE_COMMENT)) {
                $comment_preview = substr(html_unescape($tfe->stripped), 0, 50);
                $j_delete_confirm_message = json_encode("Delete comment by {$comment->owner_name}:\n$comment_preview");
                $h_delete_script = html_escape("return confirm($j_delete_confirm_message);");
                $h_delete_link = make_link("comment/delete/$i_comment_id/$i_image_id");
                $h_del = " - [<a onclick='$h_delete_script' href='$h_delete_link'>Delete</a>]";
            }
            $h_reply = "[<a href='javascript: replyTo($i_image_id, $i_comment_id, \"$i_comment_id\")'>Reply</a>]";
            $html = "<div id=\"c$i_comment_id\" class='comment'>$h_userlink$h_del $h_date No.$i_comment_id $h_reply<p>$h_comment</p></div>";
        }
        return $html;
    }
}
