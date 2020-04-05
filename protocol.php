<?php

/**
 * 网络通信协议设计
 *
 * 通信协议解决的问题：
 *      TCP协议是 流式传输协议，应用需要处理分包和合包才能有效获取数据，比如：HTTP、FTP、SMTP、Redis、MySQL 等都是基于 TCP 的协议，
 *      它们都实现了自己的数据解析方式，方便应用层进行使用。
 *
 *      Swoole底层支持 2 种类型的自定义网络通信协议：EOF结束符协议、固定包头加包体协议。
 *
 * EOF结束符协议：
 *      原理是每个数据包结尾加一串自定义的特殊字符表示数据包的结束。
 *      使用 EOF 协议，要确保数据包中间不会出现 EOF 字符，否则会分包错误。
 * ```
 * $server->set([
 *      'open_eof_split' => true,
 *      'package_eof'    => '\r\n',
 * ]);
 * ```
 *
 * 固定包头加包体协议：
 *      原理是一个数据包总是由包头和包体两部分组成。
 *      包头由一个字段 指定了包体或者整个包的长度，长度一般是使用 2 字节或 4 字节整数表示，
 *      服务器收到包头后，根据长度值来控制需要再接收多少数据才是完整的数据包。
 * ```
 * $server->set([
 *      'open_length_check'     => true,
 *      'package_max_length'    => 81920,
 *      'package_length_type'   => 'n',     // 和 pack 函数用法一致
 *      'package_length_offset' => 0,
 *      'package_body_offset'   => 2,
 * ]);
 * ```
 */






















