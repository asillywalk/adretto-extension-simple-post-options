<?php

namespace Sillynet\Adretto\SimplePostOptions;

/**
 * @phpstan-import-type MetaboxContext from Metabox
 */
class AbstractMetabox implements Metabox
{
    public static string $key = '';

    protected static string $title = '';

    /** @var MetaboxContext */
    protected static string $context = 'side';

    /** @var array<string> */
    protected static array $postTypes = ['post', 'page'];

    public function __construct()
    {
        add_filter(Hook::FILTER_METABOXES, function (array $boxes) {
            $boxes[] = $this;
            return $boxes;
        });
    }

    /**
     * @return MetaboxContext
     */
    public function getContext(): string
    {
        return static::$context;
    }

    public function getKey(): string
    {
        return static::$key;
    }

    /**
     * @return array<string>
     */
    public function getPostTypes(): array
    {
        return static::$postTypes;
    }

    public function getRenderHook(): string
    {
        return Hook::ACTION_RENDER_FIELDS . static::$key;
    }

    public function getTitle(): string
    {
        return static::$title;
    }

    public function render(\WP_Post $post): void
    {
        do_action($this->getRenderHook(), $post);
    }
}
