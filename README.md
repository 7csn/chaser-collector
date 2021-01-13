## 数据收集器

数据收集器，数据通过以 k1.k2.k3...kn 形式的键名进行递归操作（增删改）。

### 运行环境

- PHP >= 8.0

### 安装

```
composer require 7csn/collector
```

### 应用说明

* 创建数据收集器对象

    ```php
    <?php
  
    use chaser\collector;   
    use Closure;
  
    // composer 自加载，路径视具体情况而定
    require __DIR__ . '/vendor/autoload.php';
  
    // 创建数据收集器对象
    $collector = new Collector();
* 设置数据

    ```php
    $collecter->set(string $keys, mixed $value): void;
  
    $collector->set('a', 'a');
    $collector->set('b.c', 'b.c');
    ```
* 读取（无则默认，）数据

    ```php
    $collecter->get(string $keys, mixed $default = null): mixed;
  
    $collector->get('a');               // a
    $collector->get('b');               // ['c' => 'b.c']
    $collector->get('b.c');             // b.c
  
    $collector->get('c');               // null
    $collector->get('c', 'default');    // default
    $collector->get('e', fn() => 'e');  // e
    ```
* 读取（无则添加）数据

    ```php
    $collector->getOrSet(string $keys, mixed $value): mixed;
  
    $collector->getOrSet('a', fn() => 'b');     // a
    $collector->getOrSet('f', 'f');             // f
    $collector->getOrSet('g', fn() => 'g');     // g
    ```
* 删除数据

    ```php
    $collector->unset(string $keys): void;
  
    $collector->unset('b.c');
    $collector->unset('f');
    ```

* 读取全部数据

    ```php
    $collecter->list(): array;
  
    $collector->list();  // ['a'=>'a', 'b'=>[], 'c'=>'default', 'e'=>'e', 'g'=>'g']
    ```
* 清空全部数据

    ```php
    $collector->clear(): void;
    ```
* 获取序列化数据集合

    ```php
    $collector->serialize(): string;
    ```
* 反序列化修改数据集合

    ```php
    $collector->deserialize(string $data): bool
    ```
### 注意事项
* 存取数据不推荐：Closure、null
* get() 不存在数据时，默认值为 Closure 类型则取调用结果
* getOrSet() 不存在数据时，添加值为 Closure 类型则取回调结果
