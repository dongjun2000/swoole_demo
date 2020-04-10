<?php

/**
 * 协程：实现 Go 语言风格的 defer
 *
 * 由于 Go 语言没有提供析构方法，而 PHP 对象有析构函数，使用 __destory 就可以实现 Go 的风格 defer。
 *
 * 基于 PHP 对象析构方法实现的 defer 更灵活，如果希望改变执行的时机，甚至可以将 DeferTask 对象赋值给其他生命周期更长的变量，
 * defer 任务的执行可以延长生命周期。
 * 默认情况下 Go 的 defer 完全一致，在函数退出时自动执行。
 */

class DeferTask
{
    private $tasks;

    public function add(callable $fn)
    {
        $this->tasks[] = $fn;
    }

    public function __destruct()
    {
        // 反转
        $tasks = array_reverse($this->tasks);
        foreach ($tasks as $fn) {
            $fn();
        }
    }
}

function test()
{
    $o = new DeferTask();

    $h = 'hello ';
    $w = 'world!';

    // 逻辑代码
    $o->add(function () use ($w) {
        echo $w;
    });

    $o->add(function () use ($h) {
        echo $h;
    });

    // 函数结束时，对象自动析构，defer任务自动执行
}


test();

