# ReactPHP Extension for PHPStan

![Continuous Integration](https://github.com/WyriHaximus/phpstan-reactphp/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/phpstan-react/v/stable.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/phpstan-react/downloads.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)
[![License](https://poser.pugx.org/WyriHaximus/phpstan-react/license.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/phpstan-react
```

# Usage

Include the rules file from the root of this package to have PHPStan check your code for blocking functions:

```neon
includes:
	- vendor/wyrihaximus/phpstan-react/phpstan-reactphp-rules.neon
```

# Functions

## fclose


## file_exists

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\AdapterInterface::detect

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect](https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect)


## file_get_contents

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\Node\FileInterface::getContents

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents](https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents)


## file_put_contents

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\Node\FileInterface::putContents

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#putcontents](https://github.com/reactphp/filesystem/?tab=readme-ov-file#putcontents)


## fopen

Relevant package(s):

 * react/filesystem
 * react/socket

Suggested replacement(s):

 * React\Filesystem\Node\FileInterface::getContents
 * React\Socket\Connector::connect

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents](https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents)
 * [https://reactphp.org/socket/#connect](https://reactphp.org/socket/#connect)


## fread

Relevant package(s):

 * react/stream

Suggested replacement(s):

 * React\Stream\ReadableStreamInterface::on

Documentation:

 * [https://reactphp.org/stream/#data-event](https://reactphp.org/stream/#data-event)


## fwrite

Relevant package(s):

 * react/stream

Suggested replacement(s):

 * React\Stream\WritableStreamInterface::write

Documentation:

 * [https://reactphp.org/stream/#write](https://reactphp.org/stream/#write)


## gethostbyname

Relevant package(s):

 * react/dns

Suggested replacement(s):

 * React\Dns\ResolverInterface::resolve

Documentation:

 * [https://reactphp.org/dns/#resolve](https://reactphp.org/dns/#resolve)


## is_dir

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\AdapterInterface::detect

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect](https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect)


## is_file

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\AdapterInterface::detect

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect](https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect)


## is_link

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\AdapterInterface::detect

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect](https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect)


## mkdir

Relevant package(s):

 * react/filesystem

Suggested replacement(s):

 * React\Filesystem\Node\NotExistInterface::createDirectory

Documentation:

 * [https://github.com/reactphp/filesystem/?tab=readme-ov-file#createdirectory](https://github.com/reactphp/filesystem/?tab=readme-ov-file#createdirectory)


## sleep

Relevant package(s):

 * react/promise-timer

Suggested replacement(s):

 * React\Promise\Timer\sleep

Documentation:

 * [https://reactphp.org/promise-timer/#sleep](https://reactphp.org/promise-timer/#sleep)


## time_nanosleep

Relevant package(s):

 * react/promise-timer

Suggested replacement(s):

 * React\Promise\Timer\sleep

Documentation:

 * [https://reactphp.org/promise-timer/#sleep](https://reactphp.org/promise-timer/#sleep)


## time_sleep_until

Relevant package(s):

 * react/promise-timer

Suggested replacement(s):

 * React\Promise\Timer\sleep

Documentation:

 * [https://reactphp.org/promise-timer/#sleep](https://reactphp.org/promise-timer/#sleep)


## usleep

Relevant package(s):

 * react/promise-timer

Suggested replacement(s):

 * React\Promise\Timer\sleep

Documentation:

 * [https://reactphp.org/promise-timer/#sleep](https://reactphp.org/promise-timer/#sleep)


# License

The MIT License (MIT)

Copyright (c) 2024 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
