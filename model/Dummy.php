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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\kutimo\model;

use qtism\runtime\expressions\operators\OperatorProcessingException;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\String;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;

/**
 * The Dummy kutimo operator takes a single sub-expression which must have single cardinality and
 * a string or identifier baseType (empty strings/NULL accepted). This sub-expression represents
 * candidate response.
 * 
 * It returns a value with single cardinality and base-type float. This value will be:
 * 
 * * A float value of 0.0 if the sub-expression is NULL or an empty string
 * * A float value of 1.0 if the sub-expression is not NULL nor an empty string
 * 
 * Developer's note:
 * This operator exists only for testing reasons. It does not actually contact a remote
 * server for scoring.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Dummy extends CustomOperatorProcessor
{
    /**
     * Contains the business logic of the Dummy operator.
     * 
     * @throws qtism\runtime\expressions\operators\OperatorProcessingException OperatorProcessingException If more than one sub-expression is given, or the sub-expression has wrong cardinality and/or baseType.
     * @return qtism\common\datatypes\Float A Float object.
     */
    public function process()
    {
        $operands = $this->getOperands();
        
        // The operator only accepts one operand.
        if (($c = count($operands)) > 1) {
            $msg = "The 'oat.kutimo.model.Dummy' custom operator takes only one sub-expression as a parameter, ${c} given.";
            throw new OperatorProcessingException($msg, $this, OperatorProcessingException::TOO_MUCH_OPERANDS);
        } elseif (($c = count($operands)) === 0) {
            $msg = "The 'oat.kutimo.model.Dummy' custom operator takes one sub-expression as a parameter, none given.";
            throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NOT_ENOUGH_OPERANDS);
        }
        
        $operand = $operands[0];
        
        // If operand is NULL, consider it as an empty string.
        if ($operand === null) {
            $operand = new String('');
        }
        
        // The operand must have a single cardinality and have a string baseType.
        if ($operand->getCardinality() !== Cardinality::SINGLE) {
            $msg = "The 'oat.kutimo.model.Dummy' custom operator only accept a first operand with single cardinality.";
            throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
        } elseif (($c = $operand->getBaseType()) !== BaseType::STRING && $c !== BaseType::IDENTIFIER) {
            $msg = "The 'oat.kutimo.model.Dummy' custom operator only accept a first operand with string or identifier baseType.";
            throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
        }
        
        // No response -> 0.0, otherwise 1.0
        return new Float($operand->getValue() === '' ? 0.0 : 1.0);
    }
}