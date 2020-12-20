<?php

declare(strict_types=1);

namespace chaser\collector;

/**
 * 数据收集器接口
 *
 * @package chaser\collector
 */
interface CollectorInterface
{
    /**
     * 获取数据
     *
     * @param string $keys
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $keys, $default = null);

    /**
     * 获取数据（不存在执行闭包设置）
     *
     * @param string $keys
     * @param callable $closure
     * @return mixed
     */
    public function getOrSet(string $keys, callable $closure);

    /**
     * 设置数据
     *
     * @param string $keys
     * @param mixed $value
     * @return mixed
     */
    public function set(string $keys, $value);

    /**
     * 删除数据
     *
     * @param string $keys
     * @return mixed
     */
    public function unset(string $keys);

    /**
     * 清空数据集
     *
     * @return mixed
     */
    public function clear();

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
