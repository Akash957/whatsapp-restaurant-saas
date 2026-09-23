<?php

namespace App\Services\Ai;

interface AiServiceInterface
{
    public function chat(string $prompt, array $history = [], ?int $restaurantId = null): string;
    public function recommendMenu(string $query, int $restaurantId): string;
}