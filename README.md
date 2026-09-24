# ReactPHP Extension for PHPStan

[PHPStan](https://phpstan.org/) extension for [ReactPHP](https://reactphp.org/) projects: blocking PHP functions, event loop misuse, and [react/async](https://github.com/reactphp/async) fiber rules.

![Continuous Integration](https://github.com/WyriHaximus/phpstan-reactphp/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/phpstan-react/v/stable.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/phpstan-react/downloads.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)
[![License](https://poser.pugx.org/WyriHaximus/phpstan-react/license.png)](https://packagist.org/packages/WyriHaximus/phpstan-react)

## Summary:

- **PHPStan** extension for ReactPHP codebases
- **Blocking functions** with suggested async replacements
- **Event loop** rules (static proxies, no `LoopInterface` hand-off)
- **Async** `await` / `async` closure and call-graph checks
- **Stable error identifiers** for targeted ignores

## Installation:

```sh
composer require --dev wyrihaximus/phpstan-react
```

Use the [official extension-installer](https://phpstan.org/user-guide/extension-library#installing-extensions) or load the rules manually:

```neon
# phpstan.neon.dist
includes:
    - vendor/wyrihaximus/phpstan-react/extension.neon
```

## Usage:

```sh
vendor/bin/phpstan
```

> [!NOTE]
> With extension-installer, PHPStan loads this extension automatically. You only need `includes:` when you install without it.

## Configuration:

All rules from this extension are **on by default**. Override them under `parameters.wyrihaximus.reactphp.rules` in your PHPStan config. Omitting the block keeps every rule enabled.

| Parameter | Disables |
|-----------|----------|
| `blockingFunctions` | Blocking function diagnostics (`wyrihaximus.reactphp.blocking.function.*`) |
| `loopStaticProxies` | `Loop::get()` method calls (`wyrihaximus.reactphp.eventLoop.staticProxy.*`) |
| `loopInstances` | Loop instance method calls (`wyrihaximus.reactphp.eventLoop.instance.*`) |
| `passLoopInterface` | Passing `LoopInterface` into calls (`passLoopInterface`) |
| `loopInterfaceProperties` | Declaring `LoopInterface` properties (`propertyLoopInterface`) |
| `asyncAwaitInClosure` | Direct `await` in non-async closures (`awaitWithoutAsync`) |
| `asyncAwaitReachableViaCallStack` | Transitive await via the call stack (`awaitReachedWithoutAsync`) |

Each rule reports errors with a stable identifier (prefix `wyrihaximus.reactphp.`). Suppress a single finding with [PHPStan's ignoring errors](https://phpstan.org/user-guide/ignoring-errors) and the identifiers under **Rules** below.

### Full configuration example

```neon
parameters:
    level: max
    paths:
        - src
        - tests
    wyrihaximus:
        reactphp:
            rules:
                blockingFunctions: true
                loopStaticProxies: true
                loopInstances: true
                passLoopInterface: true
                loopInterfaceProperties: true
                asyncAwaitInClosure: true
                asyncAwaitReachableViaCallStack: false
```

## Rules:

### blockingFunctions
Reports blocking PHP built-ins and points at ReactPHP alternatives. Identifiers look like `wyrihaximus.reactphp.blocking.function.<name>`. The full catalog is under **Functions** below.

### loopStaticProxies
Call event loop APIs through static methods on [`React\EventLoop\Loop`](https://github.com/reactphp/event-loop), not via `Loop::get()`. Identifier prefix: `wyrihaximus.reactphp.eventLoop.staticProxy.`.

### loopInstances
Do not call loop API methods on a value typed as [`LoopInterface`](https://github.com/reactphp/event-loop/blob/1.x/src/LoopInterface.php). Prefix: `wyrihaximus.reactphp.eventLoop.instance.`.

### passLoopInterface
Do not pass `LoopInterface` into functions or constructors. Identifier: `wyrihaximus.reactphp.eventLoop.passLoopInterface`.

### loopInterfaceProperties
Do not declare properties typed as `LoopInterface` (including promoted and trait properties). Identifier: `wyrihaximus.reactphp.eventLoop.propertyLoopInterface`.

### asyncAwaitInClosure
Every closure that calls `React\Async\await` must be wrapped in `React\Async\async` where that closure is defined. Identifier: `wyrihaximus.reactphp.async.awaitWithoutAsync`.

### asyncAwaitReachableViaCallStack
Non-async closures must not reach `await` through the call stack. Identifier: `wyrihaximus.reactphp.async.awaitReachedWithoutAsync`.

See **Event loop** and **Async** later in this file for examples and detail.

## Functions:

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


## Event loop:

Do not hand the loop around. Use the static proxies on [`React\EventLoop\Loop`](https://github.com/reactphp/event-loop) instead of calling API methods on [`Loop::get()`](https://reactphp.org/event-loop/#loopget) or on any other [`LoopInterface`](https://github.com/reactphp/event-loop/blob/1.x/src/LoopInterface.php) instance.

See the [event loop documentation](https://reactphp.org/event-loop/) and the [`react/event-loop`](https://github.com/reactphp/event-loop) package.

> [!TIP]
> Prefer `Loop::addTimer(...)` over `Loop::get()->addTimer(...)` and avoid injecting `LoopInterface` into services.

### Loop::get() method calls

PHPStan reports when you invoke loop API methods through `Loop::get()`, for example `Loop::get()->addTimer(...)`. Use the matching static proxy on `Loop` instead, for example `Loop::addTimer(...)`.

Error identifiers use the prefix `wyrihaximus.reactphp.eventLoop.staticProxy.`.

### Loop instance method calls

PHPStan reports when you invoke loop API methods on a value typed as `React\EventLoop\LoopInterface`, for example `$loop->addTimer(...)` where `$loop` was injected or returned from a factory. Use the matching static proxy on `Loop` instead.

Error identifiers use the prefix `wyrihaximus.reactphp.eventLoop.instance.`.

### Passing loop instances

PHPStan reports when you pass a value typed as `React\EventLoop\LoopInterface` into any call, for example `acceptLoop(Loop::get())` or `new Service($loop)`. Do not inject or forward the loop; use the static proxies on `Loop` at the call site that needs the event loop API.

Error identifier: `wyrihaximus.reactphp.eventLoop.passLoopInterface`.

### LoopInterface properties

PHPStan reports when a class or trait declares a property typed as `React\EventLoop\LoopInterface`, including constructor-promoted properties and `@var` annotations on untyped properties. Do not store the loop on the object; use the static proxies on `Loop` where the event loop API is needed.

Error identifier: `wyrihaximus.reactphp.eventLoop.propertyLoopInterface`.


## Async:

Every closure calling `React\Async\await` has to be wrapped in `React\Async\async` where that closure is
defined. Wrapping a closure further up the call stack, or wrapping it after the fact, does not count: by the
time the closure runs there is no guarantee it is still on the fiber the wrapping set up.

The same goes for closures reaching `React\Async\await` through the methods and functions they call: the
entire call stack starting in the closure runs on the fiber `React\Async\async` sets up, so every closure
leading to an await needs that wrapping.

See the [async documentation](https://reactphp.org/async/) and the [react/async](https://github.com/reactphp/async) package.

> [!NOTE]
> Analyze the whole codebase (src and tests) so call-graph rules see real paths into `await`.

### Await inside a non-async closure

PHPStan reports when `React\Async\await` is called inside a closure that is not wrapped in `React\Async\async` at the
point where that closure is defined.

Error identifier: `wyrihaximus.reactphp.async.awaitWithoutAsync`.

### Await reached through the call stack

PHPStan reports when a non-async closure calls into code that eventually awaits, including through interfaces,
static methods, functions, and constructors. The error tip points at the await site further down the stack.

Error identifier: `wyrihaximus.reactphp.async.awaitReachedWithoutAsync`.

### await

Relevant package(s):

 * [react/async](https://github.com/reactphp/async)

Suggested replacement(s):

 * React\Async\async

Documentation:

 * [https://reactphp.org/async/#async](https://reactphp.org/async/#async)


## Contributing:

See [CONTRIBUTING.md](CONTRIBUTING.md) for local setup, `make contrib`, and pull requests.


# License

The MIT License (MIT)

Copyright (c) 2026 Cees-Jan Kiewiet

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
