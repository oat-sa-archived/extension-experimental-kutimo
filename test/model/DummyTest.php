<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

require_once dirname(__FILE__) . '/../../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

use qtism\common\datatypes\QtiDatatype;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Float;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\Variable;
use qtism\data\expressions\operators\CustomOperator;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\common\State;
use oat\kutimo\model\Dummy;

class DummyTest extends TaoPhpUnitTestRunner 
{
    private $state;
    
    private function getState()
    {
        return $this->state;
    }
    
    private function setState(State $state)
    {
        return $this->state;
    }
    
    public function tearDown()
    {
        parent::tearDown();
        $this->setState(new State());
    }
    
    /**
     * @dataProvider dummyProvider
     * 
     * @param QtiDatatype $response
     * @param Float $expected
     */
    public function testDummy(QtiDatatype $response, Float $expected)
    {
        $state = $this->getState();
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection(array($response));
        $processor = new Dummy($expression, $operands);
        $result = $processor->process();
        
        $this->assertTrue($result->equals($expected));
    }
    
    public function dummyProvider()
    {
        $zero = new Float(0.0);
        $one = new Float(1.0);
        
        return array(
            array(new String(''), $zero),
            array(new Identifier(''), $zero),
            array(new String('My candidate response...'), $one),
            array(new Identifier('CHOICE1'), $one)
        );
    }
    
    private static function createFakeExpression()
    {
        $xml = '<customOperator class="oat.kutimo.Dummy"><variable identifier="RESPONSE"/></customOperator>';
        return new CustomOperator(new ExpressionCollection(array(new Variable('RESPONSE'))), $xml);
    }
}