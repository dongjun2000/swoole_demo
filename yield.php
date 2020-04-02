<?php

/**
 * 生成器函数的核心是 yield 关键字。
 * 它最简单的调用形式看起来像一个 return申明，不同之处在于普通return会返回值并终止函数的执行，
 * 而 yield会返回一个值给循环调用此生成器的代码并且只是暂停执行生成器函数。
 *
 * 当一个生成器被调用的时候，它返回一个可以被遍历的对象。
 *
 * 当你遍历这个对象的时候（例如通过一个 foreach 循环），PHP将会在每次需要值得时候调用生成器函数，
 * 并在产生一个值之后保存生成器的状态，这样它就可以在需要产生下一个值得时候恢复调用状态。
 *
 * 一旦不再需要产生更多的值，生成器函数可以简单退出，而调用生成器的代码还可以继续执行，就像一个数组已经被遍历完了。
 *
 * 一个生成器不可以返回值：这样做会产生一个编译错误。然而return空是一个有效的语法并且它将会终止生成器继续执行。
 *
 */

// yield 语法
echo "========== yield ===========\n";
function gen_one_to_three()
{
    for ($i = 1; $i <= 3; $i++) {
        yield $i;
    }
}

$generator = gen_one_to_three();

foreach ($generator as $value) {
    echo "{$value}\n";
}

// send
echo "\n========== send ===========\n";
function nums()
{
    for ($i = 0; $i < 5; $i++) {
        $cmd = yield $i;
    }

    if ($cmd === 'stop') {
        return;
    }
}

$gen = nums();
foreach ($gen as $v) {
    if ($v === 3) {
        $gen->send('stop');
    }
    echo "{$v}\n";
}


// 生成键值对的生成器
echo "\n========== 生成键值对的生成器 ===========\n";
function input_parser($input)
{
    foreach (explode("\n", $input) as $line) {
        $fields = explode(";", $line);
        $id = array_shift($fields);         // 弹出第一列

        yield $id => $fields;
    }
}

$input = <<<'EOF'
1;PHP;Likes dollar signs
2;Python;Likes whitespace
3;Ruby;Likes blocks
EOF;

foreach (input_parser($input) as $id => $fields) {
    echo "{$id}:\n";
    echo "    $fields[0]\n";
    echo "    $fields[1]\n";
}

// 使用引用来生成值
echo "\n========== 使用引用来生成值 ===========\n";
function &gen_reference()
{
    $value = 3;

    while ($value > 0) {
        yield $value;
    }
}

foreach (gen_reference() as &$number) {
    echo (--$number) . '...';
}


// yield from  生成器委托允许通过使用 yield from 关键字执行外部生成器
echo "\n========== yield from ===========\n";
function serven()
{
    yield 7;
}

function y()
{
    yield 123;

    yield 123 => 5;

    yield;

    yield from [4, 5, 6];

    yield from serven();

    yield from new ArrayIterator([8, 9]);
}

$gen = y();

foreach ($gen as $value) {
    echo $value . PHP_EOL;
}
