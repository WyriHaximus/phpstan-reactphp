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
