<?php

declare(strict_types=1);

namespace chaser\collector;

use ArrayAccess;

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
    public function get(string $keys, $default = null)
    {
        $data = $this->dataset;

        $key = (string)strtok($keys, '.');

        do {
            if (!self::arrayAccessible($data) || !isset($data[$key])) {
                return is_callable($default) ? $default() : $default;
            }
            $data = $data[$key];
        } while (($key = strtok('.')) !== false);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getOrSet(string $keys, callable $closure)
    {
        return $this->location($this->dataset, $keys, fn(&$data, $key) => $data[$key] ??= $closure());
    }

    /**
     * @inheritDoc
     */
    public function set(string $keys, $value)
    {
        $this->location($this->dataset, $keys, fn(&$data, $key): void => $data[$key] = $value);
    }

    /**
     * @inheritDoc
     */
    public function unset(string $keys)
    {
        $this->location($this->dataset, $keys, function (&$data, $key) {
            unset($data[$key]);
        }, false);
    }

    /**
     * @inheritDoc
     */
    public function clear()
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
     * @return mixed|null
     */
    protected static function location(array &$data, string $keys, callable $callback, bool $whole = true)
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
    protected static function arrayAccessible($data): bool
    {
        return is_array($data) || $data instanceof ArrayAccess;
    }
}
