<?php

declare(strict_types=1);

final class Storage
{
    public function __construct(private readonly string $basePath)
    {
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0775, true);
        }
    }

    public function read(string $name, mixed $default = []): mixed
    {
        $path = $this->path($name);
        if (!file_exists($path)) {
            return $default;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return $default;
        }

        flock($handle, LOCK_SH);
        $contents = stream_get_contents($handle) ?: '';
        flock($handle, LOCK_UN);
        fclose($handle);

        $decoded = json_decode($contents, true);
        return $decoded === null && json_last_error() !== JSON_ERROR_NONE ? $default : $decoded;
    }

    public function write(string $name, mixed $data): void
    {
        $path = $this->path($name);
        $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            throw new RuntimeException('Could not encode JSON for ' . $name);
        }
        file_put_contents($path, $encoded . PHP_EOL, LOCK_EX);
    }

    public function update(string $name, callable $callback, mixed $default = []): mixed
    {
        $path = $this->path($name);
        $handle = fopen($path, 'c+');
        if (!$handle) {
            throw new RuntimeException('Could not open storage file: ' . $name);
        }

        flock($handle, LOCK_EX);
        $contents = stream_get_contents($handle) ?: '';
        $data = $contents === '' ? $default : json_decode($contents, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $data = $default;
        }

        $result = $callback($data);
        $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            flock($handle, LOCK_UN);
            fclose($handle);
            throw new RuntimeException('Could not encode JSON for ' . $name);
        }

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, $encoded . PHP_EOL);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return $result;
    }

    private function path(string $name): string
    {
        $safe = basename($name);
        return $this->basePath . DIRECTORY_SEPARATOR . $safe;
    }
}
