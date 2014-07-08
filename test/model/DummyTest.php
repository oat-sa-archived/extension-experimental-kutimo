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

use qtism\common\datatypes\String;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Float;
use qtism\common\enums\BaseType;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\Variable;
use qtism\data\expressions\operators\CustomOperator;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\OperatorProcessingException;
use qtism\runtime\common\MultipleContainer;
use oat\kutimo\model\Dummy;

class DummyTest extends TaoPhpUnitTestRunner 
{
    
    /**
     * @dataProvider dummyProvider
     * 
     * @param mixed $response
     * @param Float $expected
     */
    public function testDummy($response, Float $expected)
    {
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection(array($response));
        $processor = new Dummy($expression, $operands);
        $result = $processor->process();
        
        $this->assertTrue($result->equals($expected));
    }
    
    public function testNotEnoughOperands()
    {
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection();
        $processor = new Dummy($expression, $operands);
        
        $this->setExpectedException(
            'qtism\\runtime\\expressions\\operators\\OperatorProcessingException',
            "The 'oat.kutimo.model.Dummy' custom operator takes one sub-expression as a parameter, none given.",
            OperatorProcessingException::NOT_ENOUGH_OPERANDS
        );
        
        $processor->process();
    }
    
    public function testTooMuchOperands()
    {
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection(array(new String('String1'), new String('String2')));
        $processor = new Dummy($expression, $operands);
        
        $this->setExpectedException(
            'qtism\\runtime\\expressions\\operators\\OperatorProcessingException',
            "The 'oat.kutimo.model.Dummy' custom operator takes only one sub-expression as a parameter, 2 given.",
            OperatorProcessingException::TOO_MUCH_OPERANDS
        );
        
        $processor->process();
    }
    
    public function testWrongCardinality()
    {
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection(array(new MultipleContainer(BaseType::STRING)));
        $processor = new Dummy($expression, $operands);
        
        $this->setExpectedException(
            'qtism\\runtime\\expressions\\operators\\OperatorProcessingException',
            "The 'oat.kutimo.model.Dummy' custom operator only accept a first operand with single cardinality.",
            OperatorProcessingException::WRONG_CARDINALITY
        );
        
        $processor->process();
    }
    
    public function testWrongBaseType()
    {
        $expression = self::createFakeExpression();
        $operands = new OperandsCollection(array(new Float(13.37)));
        $processor = new Dummy($expression, $operands);
        
        $this->setExpectedException(
            'qtism\\runtime\\expressions\\operators\\OperatorProcessingException',
            "The 'oat.kutimo.model.Dummy' custom operator only accept a first operand with string or identifier baseType.",
            OperatorProcessingException::WRONG_BASETYPE
        );
        
        $processor->process();
    }
    
    public function dummyProvider()
    {
        $zero = new Float(0.0);
        $one = new Float(1.0);
        
        return array(
            array(new String(''), $zero),
            array(new Identifier(''), $zero),
            array(null, $zero),
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