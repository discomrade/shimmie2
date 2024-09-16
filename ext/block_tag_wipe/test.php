<?php

declare(strict_types=1);

namespace Shimmie2;

class BlockTagWipeTest extends ShimmiePHPUnitTestCase
{
    public function testBlockTagWipe(): void
    {
        self::log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", "pbx computer screenshot");
        $image = Image::by_id_ex($image_id);

        // control test
        self::get_page("post/view/$image_id");
        self::assert_title("Post $image_id: computer pbx screenshot");

        // valid change test
        send_event(new TagSetEvent($image, ["pbx", "screenshot", "monitor", "technology", "photo"]));
        self::get_page("post/view/$image_id");
        self::assert_title("Post $image_id: monitor pbx photo screenshot technology");

        // wipe test
        self::assertException(TagSetException::class, function () use ($image) {
            send_event(new TagSetEvent($image, ["This", "is", "a", "spam", "message!"]));
        });
        self::get_page("post/view/$image_id");
        self::assert_title("Post $image_id: monitor pbx photo screenshot technology");
    }
}
