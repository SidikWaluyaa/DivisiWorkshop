<?php

namespace App\Contracts;

interface MessagingService
{
    /**
     * Send a plain text message.
     */
    public function sendMessage(string $phone, string $message): array;

    /**
     * Send a template-based message.
     */
    public function sendTemplate(string $phone, string $templateId, array $parameters = []): array;
}
