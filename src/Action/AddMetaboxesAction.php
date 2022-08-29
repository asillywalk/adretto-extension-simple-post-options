<?php

namespace Sillynet\Adretto\SimplePostOptions\Action;

use Sillynet\Adretto\Action\ActionHookAction;
use Sillynet\Adretto\Action\InvokerWordpressHookAction;
use Sillynet\Adretto\SimplePostOptions\Hook;
use Sillynet\Adretto\SimplePostOptions\Metabox;

class AddMetaboxesAction extends InvokerWordpressHookAction implements
    ActionHookAction
{
    public const WP_HOOK = 'add_meta_boxes';

    public function __invoke(...$args)
    {
        /** @var array<Metabox> $boxes */
        $boxes = apply_filters(Hook::FILTER_METABOXES, []);
        // Render metaboxes for all available options on their relevant post types
        foreach ($boxes as $metaBox) {
            foreach ($metaBox->getPostTypes() as $screen) {
                add_meta_box(
                    $metaBox->getKey(),
                    $metaBox->getTitle(),
                    [$metaBox, 'render'],
                    $screen,
                    $metaBox->getContext(),
                );
            }
        }
    }
}
