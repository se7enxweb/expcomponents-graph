<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version //autogentag//
 * @filesource
 * @package Graph
 * @subpackage Tests
 */

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Expectations on the n-th call of a mock object, as PHPUnit's at() matcher
 * checked them.
 *
 * PHPUnit 9 deprecates at() (it is removed in PHPUnit 10) and reports every
 * use as a warning, which fails the run. expectsCallAt( $mock, $n, 'method' )
 * replaces "$mock->expects( $this->at( $n ) )->method( 'method' )" and keeps
 * its rules exactly:
 *
 * - $n counts every call of a mocked method of $mock, from 0;
 * - the expectation applies only when call $n is a call of 'method'; its
 *   parameters are checked at the time of the call, as with();
 * - will() gives the return value of that call;
 * - the test fails when $mock gets fewer than $n + 1 calls.
 *
 * @package Graph
 * @subpackage Tests
 */
trait ezcGraphTestInvocationAtIndex
{
    /**
     * Per mock object: its recorder and expectations.
     *
     * @var array(int=>stdClass)
     */
    private $invocationAtIndexMocks = array();

    /**
     * Expects call $index of $mock to be a call of $method, configured further
     * with with() and will().
     *
     * @param MockObject $mock
     * @param int $index
     * @param string $method
     * @return ezcGraphTestInvocationAtIndexExpectation
     */
    protected function expectsCallAt( MockObject $mock, $index, $method )
    {
        $id = spl_object_id( $mock );
        if ( !isset( $this->invocationAtIndexMocks[$id] ) )
        {
            $state = new stdClass();
            $state->mock = $mock;
            $state->calls = 0;
            $state->expectations = array();
            $state->recorder = new ezcGraphTestInvocationRecorder();
            $recorder = $state->recorder;

            $mock
                ->expects( $recorder )
                ->method( $this->anything() )
                ->will( $this->returnCallback( function () use ( $state, $recorder )
                {
                    $invocation = $recorder->last;
                    $index = $state->calls++;
                    $returnValue = $invocation->generateReturnValue();
                    foreach ( $state->expectations as $expectation )
                    {
                        if ( $expectation->index === $index && strtolower( $expectation->method ) === strtolower( $invocation->getMethodName() ) )
                        {
                            $expectation->check( $invocation );
                            if ( $expectation->stub !== null )
                            {
                                $returnValue = $expectation->stub->invoke( $invocation );
                            }
                        }
                    }
                    return $returnValue;
                } ) );
            $this->invocationAtIndexMocks[$id] = $state;
        }
        $expectation = new ezcGraphTestInvocationAtIndexExpectation( (int)$index, $method );
        $this->invocationAtIndexMocks[$id]->expectations[] = $expectation;
        return $expectation;
    }

    /**
     * Fails when a mock got fewer calls than one of its expectations needs.
     */
    protected function verifyInvocationAtIndexExpectations()
    {
        $mocks = $this->invocationAtIndexMocks;
        $this->invocationAtIndexMocks = array();
        foreach ( $mocks as $state )
        {
            foreach ( $state->expectations as $expectation )
            {
                $this->assertGreaterThan(
                    $expectation->index,
                    $state->calls,
                    sprintf( 'The expected invocation of %s at index %d was never reached.', $expectation->method, $expectation->index )
                );
            }
        }
    }

    protected function assertPostConditions() : void
    {
        parent::assertPostConditions();
        $this->verifyInvocationAtIndexExpectations();
    }
}

/**
 * One expectation of ezcGraphTestInvocationAtIndex::expectsCallAt().
 *
 * @package Graph
 * @subpackage Tests
 */
class ezcGraphTestInvocationAtIndexExpectation
{
    /** @var int */
    public $index;

    /** @var string */
    public $method;

    /** @var array(Constraint) */
    public $parameters = array();

    /** @var PHPUnit\Framework\MockObject\Stub\Stub|null */
    public $stub = null;

    public function __construct( $index, $method )
    {
        $this->index = $index;
        $this->method = $method;
    }

    /**
     * The expected parameters; values that are no constraint are compared with
     * equalTo(), as with() does.
     *
     * @return ezcGraphTestInvocationAtIndexExpectation
     */
    public function with( ...$parameters )
    {
        foreach ( $parameters as $parameter )
        {
            $this->parameters[] = $parameter instanceof Constraint ? $parameter : new IsEqual( $parameter );
        }
        return $this;
    }

    /**
     * The return value of the call, e.g. $this->returnValue( $value ).
     *
     * @return ezcGraphTestInvocationAtIndexExpectation
     */
    public function will( $stub )
    {
        $this->stub = $stub;
        return $this;
    }

    /**
     * Checks the parameters of the call, with the messages of with().
     *
     * @param Invocation $invocation
     */
    public function check( Invocation $invocation )
    {
        $actual = $invocation->getParameters();
        if ( count( $actual ) < count( $this->parameters ) )
        {
            throw new ExpectationFailedException(
                sprintf( 'Parameter count for invocation %s is too low.', $invocation->toString() )
            );
        }
        foreach ( $this->parameters as $i => $parameter )
        {
            $parameter->evaluate(
                $actual[$i],
                sprintf( 'Parameter %s for invocation %s does not match expected value.', $i, $invocation->toString() )
            );
        }
    }
}

/**
 * Matches every call of a mock object and keeps the current one, so that
 * expectsCallAt() knows which method is being called.
 *
 * @package Graph
 * @subpackage Tests
 */
class ezcGraphTestInvocationRecorder extends PHPUnit\Framework\MockObject\Rule\InvocationOrder
{
    /** @var Invocation|null */
    public $last = null;

    public function toString() : string
    {
        return 'invoked zero or more times';
    }

    public function verify() : void
    {
    }

    public function matches( Invocation $invocation ) : bool
    {
        return true;
    }

    protected function invokedDo( Invocation $invocation ) : void
    {
        $this->last = $invocation;
    }
}
?>
