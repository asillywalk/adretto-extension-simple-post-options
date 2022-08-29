<?php

namespace Sillynet\Adretto\SimplePostOptions;

interface PageOrPostOption
{
    public static function getMetaKey(): string;

    /**
     * @return mixed
     */
    public static function getValue(int $postId);

    /**
     * @param array<string, mixed> $data
     */
    public static function hasChange(?array $data): bool;

    public function getMetabox(): Metabox;

    /**
     * @param ?array<string, mixed> $data
     */
    public function onChange(int $postId, ?array $data): void;

    public function render(\WP_Post $post): void;
}
