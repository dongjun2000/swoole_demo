<?php

/**
 * 协程：setDefer机制
 * 
 * 绝大部分协程组件都支持了 setDefer 特性，可以将请求响应式的接口拆分成为两个步骤。
 * 
 * 使用此机制可以实现先发送数据，再并发收取响应结果。
 * 
 * @更多参考@doc：
 *      https://wiki.swoole.com/wiki/page/604.html
 * 
 */
