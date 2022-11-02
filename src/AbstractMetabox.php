<?php

namespace Sillynet\Adretto\SimplePostOptions;

/**
 * @phpstan-import-type MetaboxContext from Metabox
 */
abstract class AbstractMetabox implements Metabox
{
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

    abstract public function getKey(): string;

    /**
     * @return array<string>
     */
    public function getPostTypes(): array
    {
        return static::$postTypes;
    }

    public function getRenderHook(): string
    {
        return Hook::ACTION_RENDER_FIELDS . $this->getKey();
    }

    abstract public function getTitle(): string;

    public function render(\WP_Post $post): void
    {
        do_action($this->getRenderHook(), $post);
    }
}
