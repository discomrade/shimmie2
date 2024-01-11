<?php

declare(strict_types=1);

namespace Shimmie2;

class TagEditTest extends ShimmiePHPUnitTestCase
{
    public function testValidChange()
    {
        $this->log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", "pbx");
        $image = Image::by_id($image_id);

        // Original
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: pbx");

        // Modified
        send_event(new TagSetEvent($image, ["new"]));
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: new");
    }

    public function testInvalidChange()
    {
        $this->log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", "pbx");
        $image = Image::by_id($image_id);

        $e = $this->assertException(TagSetException::class, function () use ($image) {
            send_event(new TagSetEvent($image, []));
        });
        $this->assertEquals("Tried to set zero tags", $e->getMessage());

        $e = $this->assertException(TagSetException::class, function () use ($image) {
            send_event(new TagSetEvent($image, ["*test*"]));
        });
        $this->assertEquals("Can't set a tag which contains a wildcard (*)", $e->getMessage());
    }

    public function testTagEdit_tooLong()
    {
        $this->log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", str_repeat("a", 500));
        $this->get_page("post/view/$image_id");
        $this->assert_title("Post $image_id: tagme");
    }

    public function testSourceEdit()
    {
        $this->log_in_as_user();
        $image_id = $this->post_image("tests/pbx_screenshot.jpg", "pbx");
        $image = Image::by_id($image_id);

        send_event(new SourceSetEvent($image, "example.com"));
        send_event(new SourceSetEvent($image, "http://example.com"));

        $this->get_page("post/view/$image_id");
        $this->assert_text("example.com");
    }
}
