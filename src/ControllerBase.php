<?php

interface ControllerBase
{
    public function processRequest(string $method, ?string $id): void;

    public function processResourceRequest(string $method, string $id): void;

    public function processCollectionRequest(string $method);

    public function getValidationErrors(array $data, bool $is_new = true): array;
}