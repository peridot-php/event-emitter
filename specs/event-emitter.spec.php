<?php
use Peridot\EventEmitter;

describe('EventEmitter', function () {
    beforeEach(function () {
        $this->emitter = new EventEmitter();
    });

    describe('->on()', function () {
        it('can add a lambda as a listener', function () {
            $this->emitter->on('foo', function () {});
        });

        it('can add a listener method', function () {
            $listener = new Listener();
            $this->emitter->on('foo', [$listener, 'onFoo']);
        });

        it('can add a static listener method', function () {
            $this->emitter->on('bar', ['Listener', 'onBar']);
        });

        it('adds invalid listeners', function () {
            $this->emitter->on('foo', 'not a callable');
        });
    });

    describe('->once()', function () {
        it('adds a listener that fires once', function () {
            $listenerCalled = 0;

            $this->emitter->once('foo', function () use (&$listenerCalled) {
                $listenerCalled++;
            });

            assert(0 === $listenerCalled, 'expected listener not to be called');

            $this->emitter->emit('foo');

            assert(1 === $listenerCalled, 'expected listener to be called once');

            $this->emitter->emit('foo');

            assert(1 === $listenerCalled, 'expected listener to not be called again');
        });

        it('supports variadic arguments', function () {
            $capturedArgs = [];

            $this->emitter->once('foo', function ($a, $b) use (&$capturedArgs) {
                $capturedArgs = array($a, $b);
            });

            $this->emitter->emit('foo', 'a', 'b');

            assert(['a', 'b'] === $capturedArgs);
        });
    });

    describe('->emit()', function () {
        it('can emit without arguments', function () {
            $listenerCalled = false;

            $this->emitter->on('foo', function () use (&$listenerCalled) {
                $listenerCalled = true;
            });

            assert(false === $listenerCalled, 'listener should not have been called');
            $this->emitter->emit('foo');
            assert($listenerCalled, 'listener should have been called');
        });

        it('can emit with a single argument', function () {
            $listenerCalled = false;

            $this->emitter->on('foo', function ($value) use (&$listenerCalled) {
                $listenerCalled = true;
                assert('bar' === $value, 'should have received emitted argument');
            });

            assert(false === $listenerCalled, 'should not have called listener');
            $this->emitter->emit('foo', 'bar');
            assert($listenerCalled, 'listener should have been called');
        });

        it('can emit multiple arguments', function () {
            $listenerCalled = false;

            $this->emitter->on('foo', function ($arg1, $arg2) use (&$listenerCalled) {
                $listenerCalled = true;
                assert('bar', $arg1, 'listener should have received first argument');
                assert('baz', $arg2, 'listener should have received second argument');
            });

            assert(false === $listenerCalled, 'listener should not have been called');
            $this->emitter->emit('foo', 'bar', 'baz');
            assert($listenerCalled, 'listener should have been called');
        });

        it('does not require listeners', function () {
            $this->emitter->emit('foo');
            $this->emitter->emit('foo', ['bar']);
            $this->emitter->emit('foo', ['bar', 'baz']);
        });

        it('can emit to multiple listeners', function () {
            $listenersCalled = 0;

            $this->emitter->on('foo', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            $this->emitter->on('foo', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            assert(0 === $listenersCalled, 'listeners should not have been called');
            $this->emitter->emit('foo');
            assert(2 === $listenersCalled, 'listeners should have been called');
        });
    });

    describe('->removeListener()', function () {
        it('removes a matching listener', function () {
            $listenersCalled = 0;

            $listener = function () use (&$listenersCalled) {
                $listenersCalled++;
            };

            $this->emitter->on('foo', $listener);
            $this->emitter->removeListener('foo', $listener);

            assert(0 === $listenersCalled, 'listener should not have been called');
            $this->emitter->emit('foo');
            assert(0 === $listenersCalled, 'listener still should not have been called');
        });

        it('can handle a listener that is not matched', function () {
            $listenersCalled = 0;

            $listener = function () use (&$listenersCalled) {
                $listenersCalled++;
            };

            $this->emitter->on('foo', $listener);
            $this->emitter->removeListener('bar', $listener);

            assert(0 === $listenersCalled, 'listener should not have been called');
            $this->emitter->emit('foo');
            assert(1 === $listenersCalled, 'listener should have been called');
        });
    });

    describe('->removeAllListeners()', function () {
        it('removes all matching listeners', function () {
            $listenersCalled = 0;

            $this->emitter->on('foo', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            $this->emitter->removeAllListeners('foo');

            assert(0 === $listenersCalled, 'listener should not have been called');
            $this->emitter->emit('foo');
            assert(0 === $listenersCalled, 'listener still should not have been called');
        });

        it('can handle listeners that do not match', function () {
            $listenersCalled = 0;

            $this->emitter->on('foo', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            $this->emitter->removeAllListeners('bar');

            assert(0 === $listenersCalled, 'listener should not have been called');
            $this->emitter->emit('foo');
            assert(1 === $listenersCalled, 'listener should have been called');
        });

        it('can remove all listeners', function () {
            $listenersCalled = 0;

            $this->emitter->on('foo', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            $this->emitter->on('bar', function () use (&$listenersCalled) {
                $listenersCalled++;
            });

            $this->emitter->removeAllListeners();

            assert(0 === $listenersCalled, 'listeners should not have been called');
            $this->emitter->emit('foo');
            $this->emitter->emit('bar');
            assert(0 === $listenersCalled, 'listeners still should not have been called');
        });
    });
});

/**
 * Dummy listener object
 */
class Listener
{
    public function onFoo()
    {
    }

    public static function onBar()
    {
    }
}
