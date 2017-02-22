# prooph/annotations

[![Build Status](https://travis-ci.org/prooph/annotations.svg?branch=master)](https://travis-ci.org/prooph/annotations)
[![Coverage Status](https://coveralls.io/repos/prooph/annotations/badge.svg?branch=master)](https://coveralls.io/r/prooph/annotations?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

This package adds support for annotations to Prooph.

## Features

* Build aggregates and event listeners, almost without coupling to internal Prooph logic.
* Use any POPO (Plain Old PHP Object) as an aggregate.

_Note: this package is considered experimental!_

## Usage

TODO

See the example in [example/example.php](example/example.php).

### Supported annotations

This package introduces the following annotations:

* `@AggregateIdentifier`

  Should be put on a property to indicate where the aggregate identifier can be found.

* `@CommandHandler`

* `@EventHandler`

* `@TargetAggregateIdentifier`

  Should be be put on a property or method in a command (class), to indicate where the identifier of the target aggregate can be found.

### POPO aggregates

`AggregateLifecycle::recordThat`

TODO

### Links

This package was partly inspired by [Axon Framework 3.0](http://www.axonframework.org/).
