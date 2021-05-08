<?php
/**
 * See <Modern PHP>
 *
 * `Stream`: is a resource object which exhibits streamable behavior. That is, it
 * can be read from or written to in a linear fashion, and may be able to
 * fseek() to an arbitrary location within the stream
 * a stream is referenced as: scheme://target
 *
 *
 * `Wrapper`: is additional code which tells the stream how to handle specific
 * protocols/encodings: <scheme>://<target>
 * - http://
 * - file://
 * - php://
 * - sftp://
 * - 自定义
 *
 * `Filter`: is a final piece of code which may perform operations on data as it
 * is being read from or written to a stream.
 * 两种使用方式:
 * - stream_filter_append($handle, 'string.toupper')
 * - filter/read=<filter_name>/resource=<scheme>://<target>
 *
 * `Context`: is a set of `parameters` and wrapper specific `options` which modify
 * or enhance the behavior of a stream
 */

