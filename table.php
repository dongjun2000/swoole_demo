<?php

/**
 * Swoole 共享内存 Table
 * 
 * Swoole\Table:
 *      Table 是一个基于共享内存和锁实现的超高性能，并发数据结构。
 *      用于解决多进程/多线程数据共享和同步加锁问题。特点如下：
 * 
 *          1.性能高，单线程每秒可读写 200万次
 *          2.内置行锁，非全局锁
 *          3.多线程/多进程共享数据
 *          4.可用于多进程间共享数据
 *          5.实现了迭代器 Coutable 接口，可遍历和使用 count 计算行数
 * 
 * 创建内存表(1)
 *      Table->construct(int $size, float $conflict_proportion = 0.2)
 * 
 *      $size 参数执行表格的最大行数，如果 $size 不是为 2 的 N 次方，底层会自动调整为接近的一个数字，
 *      小于 1024 则默认成 1024，最小值是 1024。
 * 
 *      Table 占用内存总数 = ( 结构体长度 + key长度64字节 + 行尺寸) * 1.2 （预留的 20% 作为 hash冲突） * 列尺寸，
 *      如果机器内存不足会创建失败。
 * 
 *      (set 操作)能存储的最大行数与 $size 正相关，但不完全一致，实际小于 $size。
 * 
 * 创建内存表（2）
 *      Table->create()：bool
 *      
 *      定义好表的结构后，执行 create 向操作系统申请内存，创建表。
 *          
 *          1.调用 create 前 **不能** 使用 set/get 等数据读写操作方法。
 *          2.调用 create 后 **不能** 使用 column 方法添加新字段。
 *          3.系统内存不足会申请失败，返回 false，申请成功返回 true。
 *          4.create 必须在创建子进程之前和 Server start 之前。
 * 
 * 内存表增加一列：
 *      Table->column(string $name, int $type, int $size = 0)
 *      参数：
 *          $name  字段名
 *          $type  字段类型，支持 3 种类型，
 *                  Table::TYPE_INT, Table::TYPE_FLOAT, Table::TYPE_STRING
 *          $size  指定字符串字段的最大长度，单位是字节
 * 
 * 设置行数据：
 *      Table->set(string $key, array $value): bool
 *      参数：
 *          $key    数据的key，相同的 $key 对应同一行数据，所以相同的 key 后设置的会覆盖上一次
 *          $value  必须是一个数组，必须与字段(column)定义的 $name 完全相同，允许只修改部分，
 *                  若传入字符串长度超过字段设置的最大尺寸 ($size)，底层会自动截断（并提示 WARNING），
 *                  自动行锁。
 *      其余操作：
 *          incr    key 原子自增
 *          decr    key 原子自减
 *          exist   检查 key 是否存在
 *          del     删除指定 key 的数据
 *          count   返回 table 中存在的总行数
 * 
 * 更多@doc：
 *      https://wiki.swoole.com/wiki/page/p-table.html
 */

$table = new Swoole\Table(1024);

$table->column('id', Swoole\Table::TYPE_INT, 2);
$table->column('name', Swoole\Table::TYPE_STRING, 2);
$table->column('age', Swoole\Table::TYPE_INT, 2);

$bool = $table->create();

if (!$bool) {
    echo "Create swoole table failed\n";
} else {
    $table->set('user1', [
        'id'    => 1,
        'name'  => 'Jack', 
        'age'   => '18',
    ]);

    $table->set('user2', [
        'id'    => 3,
        'name'  => 'Tom',
        'age'   => '19',
    ]);

    $user1 = $table->get('user1');
    $user2 = $table->get('user2');

    print_r($user1);
    print_r($user2);
}