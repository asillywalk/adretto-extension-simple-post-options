<?php

namespace Sillynet\Adretto\SimplePostOptions\Service;

class NonceField
{
    private const NONCE_ACTION = 'sillynet-update-pageoptions';
    private const NONCE_NAME = 'sillynet-pageoptions-nonce';

    protected bool $hasRendered = false;

    protected bool $isValidated = false;

    /**
     * Render the WP nonce field into the current request (if it hasn't already)
     */
    public function render(): void
    {
        if (!$this->hasRendered) {
            wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
            $this->hasRendered = true;
        }
    }

    /**
     * @param array<string, mixed> $requestData  Usually the $_POST data, which
     *                                           should contain the nonce
     *                                           field's value.
     *
     * @return bool
     */
    public function validate(array $requestData): bool
    {
        if (!$this->isValidated) {
            if (
                !isset($requestData[self::NONCE_NAME]) ||
                !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)
            ) {
                $this->isValidated = true;
                return false;
            }
        }

        return true;
    }
}
