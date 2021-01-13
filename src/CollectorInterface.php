<?php

declare(strict_types=1);

namespace chaser\collector;

/**
 * 数据收集器
 *
 * @package chaser\collector
 */
interface CollectorInterface
{
    /**
     * 获取数据
     *
     * @param string $keys
     * @param mixed $default
     * @return mixed
     */
    public function get(string $keys, mixed $default = null): mixed;

    /**
     * 获取数据（不存在则添加）
     *
     * @param string $keys
     * @param mixed $value
     * @return mixed
     */
    public function getOrSet(string $keys, mixed $value): mixed;

    /**
     * 设置数据
     *
     * @param string $keys
     * @param mixed $value
     */
    public function set(string $keys, mixed $value): void;

    /**
     * 删除数据
     *
     * @param string $keys
     */
    public function unset(string $keys): void;

    /**
     * 清空数据集
     */
    public function clear(): void;

    /**
     * 获取数据集
     *
     * @return array
     */
    public function list(): array;

    /**
     * 数据集序列化
     *
     * @return string
     */
    public function serialize(): string;

    /**
     * 反序列化数据集
     *
     * @param string $data
     * @return bool
     */
    public function deserialize(string $data): bool;
}
