<?php

declare(strict_types=1);

namespace Shimmie2;

class TranscodeImageConfig extends ConfigGroup
{
    public ?string $title = "Transcode Images";

    #[ConfigMeta("Version", ConfigType::INT, advanced: true)]
    public const VERSION = "ext_transcode_version";

    #[ConfigMeta("Allow transcoding images", ConfigType::BOOL)]
    public const ENABLED = "transcode_enabled";

    #[ConfigMeta("Enable GET args", ConfigType::BOOL)]
    public const GET_ENABLED = "transcode_get_enabled";

    #[ConfigMeta("Transcode on upload", ConfigType::BOOL)]
    public const UPLOAD = "transcode_upload";

    #[ConfigMeta("Engine", ConfigType::STRING, options: ["GD" => "gd", "ImageMagick" => "convert"])]
    public const ENGINE = "transcode_engine";

    #[ConfigMeta("Lossy Format Quality", ConfigType::INT)]
    public const QUALITY = "transcode_quality";

    #[ConfigMeta("Alpha Conversion Color", ConfigType::STRING, ui_type: "color")]
    public const ALPHA_COLOR = "transcode_alpha_color";

    /**
     * @return array<string, ConfigMeta>
     */
    public function get_config_fields(): array
    {
        global $config;
        $fields = parent::get_config_fields();

        $engine = $config->get_string(TranscodeImageConfig::ENGINE);
        foreach (TranscodeImage::INPUT_MIMES as $display => $mime) {
            if (MediaEngine::is_input_supported($engine, $mime)) {
                $outputs = TranscodeImage::get_supported_output_mimes($engine, $mime);
                $fields[TranscodeImage::get_mapping_name($mime)] = new ConfigMeta(
                    $display,
                    ConfigType::STRING,
                    options: $outputs
                );
            }
        }

        return $fields;
    }
}
