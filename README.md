Semaphore Messenger Transport
=============================

[![Latest Stable Version](https://poser.pugx.org/coka/semaphore-messenger/v/stable)](https://packagist.org/packages/coka/semaphore-messenger)
[![Total Downloads](https://poser.pugx.org/coka/semaphore-messenger/downloads)](https://packagist.org/packages/coka/semaphore-messenger)
[![Latest Unstable Version](https://poser.pugx.org/coka/semaphore-messenger/v/unstable)](https://packagist.org/packages/coka/semaphore-messenger)
[![License](https://poser.pugx.org/coka/semaphore-messenger/license)](https://packagist.org/packages/coka/semaphore-messenger)
[![Monthly Downloads](https://poser.pugx.org/coka/semaphore-messenger/d/monthly)](https://packagist.org/packages/coka/semaphore-messenger)
[![Daily Downloads](https://poser.pugx.org/coka/semaphore-messenger/d/daily)](https://packagist.org/packages/coka/semaphore-messenger)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b4592a7-61ca-40ac-b4f1-e14818ec5d7e/mini.png)](https://insight.sensiolabs.com/projects/8b4592a7-61ca-40ac-b4f1-e14818ec5d7e)
[![Travis CI](https://travis-ci.org/CedrickOka/rest-request-validator-bundle.svg?branch=master)](https://travis-ci.org/CedrickOka/rest-request-validator-bundle)

The Semaphore Transport allows you to send and receive messages on System V message queues. The semaphore transport configuration looks like this :

```bash
# .env
MESSENGER_TRANSPORT_DSN=semaphore://%kernel.project_dir%/.env
# Full DSN Example
MESSENGER_TRANSPORT_DSN=semaphore://%kernel.project_dir%/.env?project=M&message_type=1&message_max_size=1024
```

A number of options can be configured via the DSN or via the options key under the transport in messenger.yaml:

Option | Description | Default 
-------- | --------------- | ---------
path | Pathname to create System V IPC key | 
project | Project identifier to create System V IPC key | M
message_type | The type of message to send | 1
message_max_size | The maximum size in bytes of a message if the message is larger than this size, an exception will be thrown | 131072
auto_setup | Enable or not the auto-setup of queue | true

## Requirements

The Semaphore module together with [`ext-sysvmsg`](https://www.php.net/manual/en/sem.installation.php) must be installed in order for this package to work.

> This extension is not available on Windows platforms.

## Installation

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require coka/semaphore-messenger
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Configuration

You can register the semaphore transport factory to be able to use it via a DSN in the Symfony application.

### Register the Semaphore Transport Factory

```yaml
# config/services.yaml
services:
    Oka\Messenger\Transport\Semaphore\SemaphoreTransportFactory:
        tags: [messenger.transport_factory]
```

### Use your Transport

Within the `framework.messenger.transports.*` configuration, create your named transport using your own DSN:
 
```php
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            yours: 'semaphore://...'
```

## Copyright and License

The coka/semaphore-messenger library is copyright © Baidai Cedrick Oka <https://github.com/CedrickOka> and licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[source](https://github.com/CedrickOka/semaphore-messenger)
[release](https://packagist.org/packages/coka/semaphore-messenger)
[license](https://github.com/CedrickOka/semaphore-messenger/blob/master/LICENSE)
[build](https://travis-ci.org/CedrickOka/semaphore-messenger)
[downloads](https://packagist.org/packages/coka/semaphore-messenger)
