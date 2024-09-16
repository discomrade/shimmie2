<?php

declare(strict_types=1);

namespace Shimmie2;

class BlockTagWipeInfo extends ExtensionInfo
{
    public const KEY = "block_tag_wipe";

    public string $key = self::KEY;
    public string $name = "Block Tag Wipe";
    public array $authors = ["Discomrade" => ""];
    public string $license = self::LICENSE_GPLV2;
    public string $description = "For blocking spam and trolls wiping tags.";
    public ?string $documentation =
        "This extension blocks a spammer or troll from removing all
of a post's existing tags at once, if more than two exist.
This extention was made to counter a commercial spammer who opens a
recently commented post, wipes the tags and sends their message instead.
It currently only runs for anons or accounts made within 24 hours";
    public ExtensionCategory $category = ExtensionCategory::MODERATION;
}
