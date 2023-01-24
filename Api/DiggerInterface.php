<?php

declare(strict_types = 1);

namespace Digtective\Digger\Api;

interface DiggerInterface
{
    /**
     * POST for registerPageView api
     *
     * @param string $currentPath
     * @param string $referer
     * @return void
     */
    public function registerPageView($currentPath, $referer);
}
