<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * System section for status and health monitoring.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class SystemSection extends AbstractSection
{
    /**
     * Get system status.
     *
     * @return string|array API response
     */
    public function status(): string|array
    {
        return $this->get('status');
    }

    /**
     * Get system health.
     *
     * @return string|array API response
     */
    public function health(): string|array
    {
        return $this->get('health');
    }
}
