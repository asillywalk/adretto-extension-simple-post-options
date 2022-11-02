<?php

namespace Sillynet\Adretto\SimplePostOptions;

use Gebruederheitz\Wordpress\MetaFields\MetaForms;

abstract class AbstractBooleanPageOrPostOption extends AbstractPageOrPostOption
{
    /**
     * @param mixed $data
     */
    public static function hasChange($data): bool
    {
        // Checkbox fields should always update
        return true;
    }

    public function render(\WP_Post $post): void
    {
        $this->nonceField->render();
        $checked = $this->getValue($post->ID);
        MetaForms::makeCheckboxField(static::getKey())
            ->setChecked($checked)
            ->setLabel(static::getInputLabel())
            ->render();
    }

    /**
     * @param mixed $rawValue
     */
    public static function parseValue($rawValue): bool
    {
        return (bool) $rawValue;
    }

    /**
     * @param ?array<string, mixed> $data
     */
    public function onChange(int $postId, ?array $data): void
    {
        $value =
            isset($data[static::getKey()]) && $data[static::getKey()] === 'on';
        update_post_meta($postId, static::getMetaKey(), $value);
    }

    public static function getValue(int $postId): bool
    {
        return (bool) parent::getValue($postId); // TODO: Change the autogenerated stub
    }
}
