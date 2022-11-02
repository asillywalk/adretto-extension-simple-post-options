<?php

namespace Sillynet\Adretto\SimplePostOptions;

use Gebruederheitz\Wordpress\MetaFields\Exception\InvalidFieldConfigurationException;
use Gebruederheitz\Wordpress\MetaFields\MetaForms;
use Psr\Container\ContainerInterface;
use Sillynet\Adretto\SimplePostOptions\Service\NonceField;
use Sillynet\Adretto\Theme;
use WP_Post;

abstract class AbstractPageOrPostOption implements PageOrPostOption
{
    protected static bool $hasLinks = false;

    protected Metabox $metabox;

    protected NonceField $nonceField;

    abstract protected static function getKey(): string;

    abstract protected static function getInputLabel(): string;

    /** @return class-string */
    abstract protected static function getMetaboxClassName(): string;

    public function __construct(NonceField $nonceField, ContainerInterface $c)
    {
        $this->nonceField = $nonceField;

        /** @var Metabox $metabox */
        $metabox = $c->get(static::getMetaboxClassName());
        $this->metabox = $metabox;

        add_action($this->metabox->getRenderHook(), [$this, 'render']);
        add_filter(Hook::FILTER_POST_OPTIONS, function (array $options) {
            $options[] = $this;

            return $options;
        });

        if (static::$hasLinks) {
            $this->initScripts();
        }

        // make the option available as a service
        Theme::getInstance()
            ->getContainer()
            ->set(static::class, $this);
    }

    /**
     * @return mixed
     */
    public static function getValue(int $postId)
    {
        $originalValue = get_post_meta($postId, static::getMetaKey(), true);
        return static::parseValue($originalValue);
    }

    /**
     * @param ?array<string, mixed> $data
     */
    abstract public static function hasChange(?array $data): bool;

    /**
     * @param mixed $rawValue
     *
     * @return mixed
     */
    public static function parseValue($rawValue)
    {
        return $rawValue;
    }

    public function getMetabox(): Metabox
    {
        return $this->metabox;
    }

    public function onEnqueueScripts(string $hook): void
    {
        global $post;

        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ($post->post_type === 'page' || $post->post_type === 'post') {
                wp_enqueue_script('wp-link');
            }
        }
    }

    /**
     * @param ?array<string, mixed> $data
     */
    public function onChange(int $postId, ?array $data): void
    {
        $rawValue = $data[static::getKey()] ?? null;
        $value =
            isset($rawValue) && is_string($rawValue)
                ? sanitize_text_field($rawValue)
                : '';

        update_post_meta($postId, static::getMetaKey(), $value);
    }

    /**
     * @throws InvalidFieldConfigurationException
     */
    public function render(WP_Post $post): void
    {
        $this->nonceField->render();
        $rawValue = $this->getValue($post->ID);
        $value = is_string($rawValue) ? $rawValue : '';

        MetaForms::makeTextInputField()
            ->setName(static::getKey())
            ->setValue($value)
            ->setLabel(static::getInputLabel())
            ->render();
    }

    public static function getMetaKey(): string
    {
        return '_' . static::getKey();
    }

    private function initScripts(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'onEnqueueScripts'], 10, 1);
    }
}
