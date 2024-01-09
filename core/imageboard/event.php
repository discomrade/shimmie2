<?php

declare(strict_types=1);

namespace Shimmie2;

/**
 * An image is being added to the database.
 */
class ImageAdditionEvent extends Event
{
    /**
     * Inserts a new image into the database with its associated
     * information.
     *
     * @param mixed[] $metadata
     */
    public function __construct(
        public Image $image,
        public array $metadata,
    ) {
        parent::__construct();
    }
}

/**
 * An image is being deleted.
 */
class ImageDeletionEvent extends Event
{
    /**
     * Deletes an image.
     *
     * Used by things like tags and comments handlers to
     * clean out related rows in their tables.
     */
    public function __construct(
        public Image $image,
        public bool $force = false,
    ) {
        parent::__construct();
    }
}

/**
 * An image is being replaced.
 */
class ImageReplaceEvent extends Event
{
    public string $old_hash;
    public string $new_hash;

    /**
     * Replaces an image file.
     *
     * Updates an existing ID in the database to use a new image
     * file, leaving the tags and such unchanged. Also removes
     * the old image file and thumbnail from the disk.
     */
    public function __construct(
        public Image $image,
        public string $tmp_filename,
    ) {
        parent::__construct();
        $this->old_hash = $image->hash;
        $this->new_hash = md5_file($tmp_filename);
    }
}

class ImageReplaceException extends SCoreException
{
}

/**
 * Request a thumbnail be made for an image object.
 */
class ThumbnailGenerationEvent extends Event
{
    public bool $generated;

    /**
     * Request a thumbnail be made for an image object
     */
    public function __construct(
        public Image $image,
        public bool $force = false
    ) {
        parent::__construct();
        $this->generated = false;
    }
}


/*
 * ParseLinkTemplateEvent:
 *   $link     -- the formatted text (with each element URL Escape'd)
 *   $text     -- the formatted text (not escaped)
 *   $original -- the formatting string, for reference
 *   $image    -- the image who's link is being parsed
 */
class ParseLinkTemplateEvent extends Event
{
    public string $link;
    public string $text;
    public string $original;
    public Image $image;

    public function __construct(string $link, Image $image)
    {
        parent::__construct();
        $this->link = $link;
        $this->text = $link;
        $this->original = $link;
        $this->image = $image;
    }

    public function replace(string $needle, ?string $replace): void
    {
        if (!is_null($replace)) {
            $this->link = str_replace($needle, url_escape($replace), $this->link);
            $this->text = str_replace($needle, $replace, $this->text);
        }
    }
}
