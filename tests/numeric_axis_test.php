<?php
/**
 * ezcGraphNumericAxisTest
 *
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
 * @package Graph
 * @version //autogen//
 * @subpackage Tests
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

/**
 * Tests for ezcGraph class.
 *
 * @package Graph
 * @subpackage Tests
 */
class ezcGraphNumericAxisTest extends ezcTestCase
{
	public static function suite()
	{
		return new \PHPUnit\Framework\TestSuite( "ezcGraphNumericAxisTest" );
	}

    public function testFactoryNumericAxis()
    {
        $chart = new ezcGraphLineChart();

        $this->assertTrue(
            $chart->yAxis instanceof ezcGraphChartElementNumericAxis
        );
    }

    public function testGetSteps()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 70, 12, 39, 87 ) );
        $chart->render( 500, 200 );

        $steps = $chart->yAxis->getSteps();

        $position = 0.;
        $label = 0.;
        foreach ( $steps as $nr => $step )
        {
            $this->assertEqualsWithDelta(
                $step->position,
                $position,
                .00001,
                "[$nr] Step position wrong."
            );
            $position += .25;

            $this->assertEqualsWithDelta(
                $step->width,
                .25,
                .00001,
                "[$nr] Step width wrong."
            );

            $this->assertEqualsWithDelta(
                $step->label,
                $label,
                .00001,
                "[$nr] Step label wrong."
            );
            $label += 25;

            if ( $nr < ( count( $steps ) - 1 ) )
            {
                $this->assertSame(
                    count( $step->childs ),
                    4,
                    "[$nr] Step child count wrong."
                );
            }
        }
    }

    public function testManualScaling()
    {
        $chart = new ezcGraphLineChart();
        $chart->yAxis->min = 10;
        $chart->yAxis->max = 50;
        $chart->yAxis->majorStep = 10;
        $chart->yAxis->minorStep = 1;

        $this->assertEquals(
            10.,
            $chart->yAxis->min
        );

        $this->assertEquals(
            50.,
            $chart->yAxis->max
        );

        $this->assertEquals(
            10.,
            $chart->yAxis->majorStep
        );

        $this->assertEquals(
            1.,
            $chart->yAxis->minorStep
        );
    }

    public function testManualScalingPublicProperties()
    {
        $chart = new ezcGraphLineChart();
        $chart->yAxis->min = 10;
        $chart->yAxis->max = 50;
        $chart->yAxis->majorStep = 10;
        $chart->yAxis->minorStep = 1;

        $this->assertEqualsWithDelta(
            10.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            50.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            10.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testAutomagicScalingSingle()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 20, 70, 12, 130 ) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            150.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            25.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            5.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testAutomagicScalingSingle2()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1, 4.3, .2, 3.82 ) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            5.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            .25,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testAutomagicScalingSingle3()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => -1.8, 4.3, .2, 3.82 ) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            -2.5,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            5.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            2.5,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            .5,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testAutomagicScalingSingle4()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1000.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1500.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            100.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            25.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testAutomagicScalingMultiple()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->data['sample2'] = new ezcGraphArrayDataSet( array( 2000 => 1270, 1170, 1610, 1370 ) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1000.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1750.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            250.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            50.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->majorStep = 50;
        $chart->render( 500, 300 );

        $this->assertEqualsWithDelta(
            1000.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1450.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            50.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            10.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling2()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->min = 0;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1500.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            500.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            100.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling3()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->max = 2000;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1000.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            2000.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            250.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            50.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling4()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->min = 0;
        $chart->yAxis->max = 2000;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            2000.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            1000.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            250.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling5()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 4.5 ) );
        $chart->yAxis->majorStep = .5;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            4.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            5.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            .5,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            .1,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling6()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 113.5 ) );
        $chart->yAxis->majorStep = .5;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            113.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            114.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            .5,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            .1,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling7()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 113.5, 1800, -45 ) );
        $chart->yAxis->majorStep = 500;
        $chart->yAxis->min = -100;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            -100,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1900,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            500,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            100,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling8()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 113.5, 1800, -45 ) );
        $chart->yAxis->majorStep = 500;
        $chart->yAxis->max = 1900;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            -100,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1900,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            500,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            100,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    /**
     * Tests bug #12581
     *
     * @return void
     */
    public function testMixedAutomagicAndManualScaling9()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array(
            'Material1' => 10,
            'Material2' => 11.1,
            'Material3' => 11.1,
            'Material4' => 9.7,
            'Material5' => 12.3,
            'Material6' => 6.4,
            'Material7' => 5.8,
            'Material8' => 5.4
        ) );
        $chart->yAxis->min = 0;
        $chart->yAxis->max = 13.53;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            13.53,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            2.706,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            0.5412,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling10()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->min = 0;
        $chart->yAxis->max = 2000;
        $chart->yAxis->majorStep = 250;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            2000.,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            250.,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            50.,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScaling11()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( "1.2" => 3, "1.25" => 1 ) );
        $chart->yAxis->min = 1.175;
        $chart->yAxis->max = 1.275;
        $chart->yAxis->majorStep = 0.025;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1.175,
            $chart->yAxis->min,
            .001,
            'As value for: min; '
        );

        $this->assertEqualsWithDelta(
            1.275,
            $chart->yAxis->max,
            .001,
            'As value for: max; '
        );

        $this->assertEqualsWithDelta(
            0.025,
            $chart->yAxis->majorStep,
            .001,
            'As value for: majorStep; '
        );

        $this->assertEqualsWithDelta(
            .005,
            $chart->yAxis->minorStep,
            .001,
            'As value for: minorStep; '
        );
    }

    public function testMixedAutomagicAndManualScalingStepSizeFailure1()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->min = 0;
        $chart->yAxis->max = 2000;
        $chart->yAxis->majorStep = 300;

        try
        {
            $chart->render( 500, 200 );
            $this->fail( 'Expected ezcGraphInvalidStepSizeException.' );
        }
        catch ( ezcGraphInvalidStepSizeException $e )
        { /* Expected */ }
    }

    public function testMixedAutomagicAndManualScalingStepSizeFailure2()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->min = 0;
        $chart->yAxis->max = 2000;
        $chart->yAxis->majorStep = 250;
        $chart->yAxis->minorStep = 100;

        try
        {
            $chart->render( 500, 200 );
            $this->fail( 'Expected ezcGraphInvalidStepSizeException.' );
        }
        catch ( ezcGraphInvalidStepSizeException $e )
        { /* Expected */ }
    }

    public function testPositionLeft()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->position = ezcGraph::LEFT;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position. '
        );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( 1000 ),
            .05,
            'Wrong minimal value. '
        );

        $this->assertEqualsWithDelta(
            .4,
            $chart->yAxis->getCoordinate( 1200 ),
            .05,
            'Wrong mid value. '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( 1500 ),
            .05,
            'Wrong maximum value. '
        );
    }

    public function testPositionRight()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->position = ezcGraph::RIGHT;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position. '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( 1000 ),
            .05,
            'Wrong minimal value. '
        );

        $this->assertEqualsWithDelta(
            .6,
            $chart->yAxis->getCoordinate( 1200 ),
            .05,
            'Wrong mid value. '
        );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( 1500 ),
            .05,
            'Wrong maximum value. '
        );
    }

    public function testPositionTop()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->position = ezcGraph::TOP;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position. '
        );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( 1000 ),
            .05,
            'Wrong minimal value. '
        );

        $this->assertEqualsWithDelta(
            0.4,
            $chart->yAxis->getCoordinate( 1200 ),
            .05,
            'Wrong mid value. '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( 1500 ),
            .05,
            'Wrong maximum value. '
        );
    }

    public function testPositionBottom()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => 1045, 1300, 1012, 1450 ) );
        $chart->yAxis->position = ezcGraph::BOTTOM;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position. '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( 1000 ),
            .05,
            'Wrong minimal value. '
        );

        $this->assertEqualsWithDelta(
            .6,
            $chart->yAxis->getCoordinate( 1200 ),
            .05,
            'Wrong mid value. '
        );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( 1500 ),
            .05,
            'Wrong maximum value. '
        );
    }

    public function testPositionLeftNegativMinimum()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( array( 2000 => -300, 1300, 1012, 1450 ) );
        $chart->yAxis->majorStep = 500;
        $chart->yAxis->position = ezcGraph::LEFT;
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            .25,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position. '
        );

        $this->assertEqualsWithDelta(
            0.,
            $chart->yAxis->getCoordinate( -500 ),
            .05,
            'Wrong minimal value. '
        );

        $this->assertEqualsWithDelta(
            .85,
            $chart->yAxis->getCoordinate( 1200 ),
            .05,
            'Wrong mid value. '
        );

        $this->assertEqualsWithDelta(
            1.,
            $chart->yAxis->getCoordinate( 1500 ),
            .05,
            'Wrong maximum value. '
        );
    }

    public function testNullPositionMultipleDataSets()
    {
        $chart = new ezcGraphLineChart();
        $chart->data['sampleData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => -21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
        $chart->data['moreData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 112, 'sample 2' => 54, 'sample 3' => 12, 'sample 4' => -167, 'sample 5' => 329) );
        $chart->data['evenMoreData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 300, 'sample 2' => -30, 'sample 3' => 220, 'sample 4' => 67, 'sample 5' => 450) );
        $chart->render( 500, 200 );

        $this->assertEqualsWithDelta(
            0.66,
            $chart->yAxis->getCoordinate( false ),
            .05,
            'Wrong initial axis position.'
        );
    }

    public function testChartElementNumericAxisPropertyMin()
    {
        $options = new ezcGraphChartElementNumericAxis();

        $this->assertSame(
            null,
            $options->min,
            'Wrong default value for property min in class ezcGraphChartElementNumericAxis'
        );

        $options->min = 1;
        $this->assertSame(
            1.,
            $options->min,
            'Setting property value did not work for property min in class ezcGraphChartElementNumericAxis'
        );

        try
        {
            $options->min = false;
        }
        catch ( ezcBaseValueException $e )
        {
            return true;
        }

        $this->fail( 'Expected ezcBaseValueException.' );
    }

    public function testChartElementNumericAxisPropertyMax()
    {
        $options = new ezcGraphChartElementNumericAxis();

        $this->assertSame(
            null,
            $options->max,
            'Wrong default value for property max in class ezcGraphChartElementNumericAxis'
        );

        $options->max = 1;
        $this->assertSame(
            1.,
            $options->max,
            'Setting property value did not work for property max in class ezcGraphChartElementNumericAxis'
        );

        try
        {
            $options->max = false;
        }
        catch ( ezcBaseValueException $e )
        {
            return true;
        }

        $this->fail( 'Expected ezcBaseValueException.' );
    }

    public function testRenderedLabels()
    {
        try
        {
            $chart = new ezcGraphLineChart();
            $chart->data['sampleData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => -21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
            $chart->render( 500, 200 );
        }
        catch ( ezcGraphFontRenderingException $e )
        {
            // Ignore
        }

        $steps = $chart->yAxis->getSteps();

        $expectedLabels = array(
            '-100', '0', '100', '200', '300', '400',
        );

        foreach ( $steps as $nr => $step )
        {
            $this->assertEquals(
                $step->label,
                $expectedLabels[$nr],
                'Label not as expected'
            );
        }
    }

    public function testRenderedLabelsWithLabelFormattingCallback()
    {
        try
        {
            $chart = new ezcGraphLineChart();

            $chart->yAxis->labelCallback = function ($label) {
													return "*{$label}*";
												};

            $chart->data['sampleData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => -21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
            $chart->render( 500, 200 );
        }
        catch ( ezcGraphFontRenderingException $e )
        {
            // Ignore
        }

        $steps = $chart->yAxis->getSteps();

        $expectedLabels = array(
            '*-100*', '*0*', '*100*', '*200*', '*300*', '*400*',
        );

        foreach ( $steps as $nr => $step )
        {
            $this->assertSame(
                $step->label,
                $expectedLabels[$nr],
                'Label not as expected'
            );
        }
    }

    public function testRenderedLabelsWithLabelFormatString()
    {
        try
        {
            $chart = new ezcGraphLineChart();

            $chart->yAxis->formatString = '%d $';

            $chart->data['sampleData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => -21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
            $chart->render( 500, 200 );
        }
        catch ( ezcGraphFontRenderingException $e )
        {
            // Ignore
        }

        $steps = $chart->yAxis->getSteps();

        $expectedLabels = array(
            '-100 $', '0 $', '100 $', '200 $', '300 $', '400 $',
        );

        foreach ( $steps as $nr => $step )
        {
            $this->assertSame(
                $step->label,
                $expectedLabels[$nr],
                'Label not as expected'
            );
        }
    }
}

?>
