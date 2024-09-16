<?php

declare(strict_types=1);

namespace Shimmie2;

class BlockTagWipeTest extends ShimmiePHPUnitTestCase
{
    public function testBlockTagWipe(): void
    {
        global $config;

        $this->log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", "pbx computer screenshot");
        $image = Image::by_id_ex($image_id);

        // control test
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: computer pbx screenshot");

        // valid change test
        send_event(new TagSetEvent($image, ["pbx", "screenshot", "monitor", "technology", "photo"]));
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: monitor pbx photo screenshot technology");

        // wipe test
        $this->assertException(TagSetException::class, function () use ($image) {
            send_event(new TagSetEvent($image, ["This", "is", "a", "spam", "message!"]));
        });
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: monitor pbx photo screenshot technology");
    }
}
