<?php

use Sillynet\Adretto\SimplePostOptions\Action\AddMetaboxesAction;
use Sillynet\Adretto\SimplePostOptions\Action\SavePostOptionsAction;
use Sillynet\Adretto\Theme;

Theme::getInstance()->addAction(AddMetaboxesAction::class);
Theme::getInstance()->addAction(SavePostOptionsAction::class);
