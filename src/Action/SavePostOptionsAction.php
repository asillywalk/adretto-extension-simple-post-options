<?php

namespace Sillynet\Adretto\SimplePostOptions\Action;

use Sillynet\Adretto\Action\ActionHookAction;
use Sillynet\Adretto\Action\InvokerWordpressHookAction;
use Sillynet\Adretto\SimplePostOptions\Hook;
use Sillynet\Adretto\SimplePostOptions\PageOrPostOption;
use Sillynet\Adretto\SimplePostOptions\Service\NonceField;
use WP_Post;

class SavePostOptionsAction extends InvokerWordpressHookAction implements
    ActionHookAction
{
    public const WP_HOOK = 'save_post';

    public const ARGUMENT_COUNT = 2;

    protected NonceField $nonceField;

    public function __construct(NonceField $nonceField)
    {
        $this->nonceField = $nonceField;
    }

    /**
     * Calls the onChange method on each PageOption with a changed value if
     * the nonce field is valid and the user has sufficient capabilities.
     *
     * @param mixed|object ...$args
     */
    public function __invoke(...$args): ?int
    {
        $postId = $args[0];
        $post = $args[1];

        if (
            !is_int($postId) ||
            !(is_object($post) && is_a($post, WP_Post::class))
        ) {
            return null;
        }

        if (!$this->nonceField->validate($_POST)) {
            return $postId;
        }

        $postType = get_post_type_object($post->post_type);
        if (!$postType) {
            return $postId;
        }

        if (!current_user_can($postType->cap->edit_post, $postId)) {
            return $postId;
        }

        /** @var array<PageOrPostOption> $options */
        $options = apply_filters(Hook::FILTER_POST_OPTIONS, []);

        foreach ($options as $option) {
            if (
                in_array($postType->name, $option->getMetabox()->getPostTypes())
            ) {
                $option->onChange($postId, $_POST);
            }
        }

        return null;
    }
}
