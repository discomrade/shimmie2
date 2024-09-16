<?php

declare(strict_types=1);

namespace Shimmie2;

class BlockTagWipe extends Extension
{
    public function onTagSet(TagSetEvent $event): void
    {
        global $user;
        // check first for anon or account created with 24 hours, use <= 1 in case they accidentally retain `tagme`
        if (($user->is_anonymous() || (time() - strtotime($user->join_date) < 43200)) && (count($event->old_tags) > 2) && count(array_intersect($event->old_tags, $event->new_tags)) <= 1) {
            throw new TagSetException("Submitted. Please wait for moderator approval for this change.");
        }
    }

    public function get_priority(): int
    {
        return 30;
    }
}
