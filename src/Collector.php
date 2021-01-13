<?php

declare(strict_types=1);

namespace chaser\collector;

use ArrayAccess;
use Closure;

/**
 * 数据收集器类
 *
 * @package chaser\collector
 */
class Collector implements CollectorInterface
{
    /**
     * 数据集
     *
     * @var array
     */
    protected array $dataset = [];

    /**
     * @inheritDoc
     */
    public function get(string $keys, mixed $default = null): mixed
    {
        $data = $this->dataset;

        $key = (string)strtok($keys, '.');

        do {
            if (!self::arrayAccessible($data) || !isset($data[$key])) {
                return $default instanceof Closure ? $default() : $default;
            }
            $data = $data[$key];
        } while (false !== $key = strtok('.'));

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getOrSet(string $keys, mixed $value): mixed
    {
        return $this->location(
            $this->dataset,
            $keys,
            fn(&$data, $key): mixed => $data[$key] ??= $value instanceof Closure ? $value() : $value
        );
    }

    /**
     * @inheritDoc
     */
    public function set(string $keys, mixed $value): void
    {
        $this->location($this->dataset, $keys, fn(&$data, $key): void => $data[$key] = $value);
    }

    /**
     * @inheritDoc
     */
    public function unset(string $keys): void
    {
        $this->location($this->dataset, $keys, function (&$data, $key) {
            unset($data[$key]);
        }, false);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->dataset = [];
    }

    /**
     * @inheritDoc
     */
    public function list(): array
    {
        return $this->dataset;
    }

    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return serialize($this->dataset);
    }

    /**
     * @inheritDoc
     */
    public function deserialize(string $data): bool
    {
        $op = true;
        set_error_handler(function () use (&$op) {
            $op = false;
        });
        $this->dataset = unserialize($data) ?: [];
        restore_error_handler();
        return $op;
    }

    /**
     * 数据元素定位处理
     *
     * @param array $data
     * @param string $keys
     * @param callable $callback
     * @param bool $whole
     * @return mixed
     */
    private function location(array &$data, string $keys, callable $callback, bool $whole = true): mixed
    {
        $keys = explode('.', $keys);
        $count = count($keys);

        while ($count-- > 1) {
            $key = array_shift($keys);
            if (!isset($data[$key]) || !self::arrayAccessible($data[$key])) {
                if (!$whole) {
                    return null;
                }
                $data[$key] = [];
            }
            $data = &$data[$key];
        }

        return $callback($data, array_shift($keys));
    }

    /**
     * 数据是否可以数组形式访问
     *
     * @param mixed $data
     * @return bool
     */
    private function arrayAccessible(mixed $data): bool
    {
        return is_array($data) || $data instanceof ArrayAccess;
    }
}
