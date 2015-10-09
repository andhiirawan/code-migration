<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Migration\Code\Processor\Table;

use Magento\Migration\Code\Processor\Table\TableFunctionMatcher;

/**
 * Class TableFunctionMatcherTest
 */
class TableFunctionMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Migration\Code\Processor\Table\TableFunctionMatcher
     */
    protected $model;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $classCbjectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenHelper;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Framework\ObjectManagerInterface';
        $this->classCbjectManager = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Migration\Logger\Logger';
        $this->logger = $this->getMock($className, [], [], '', false);

        $className = 'Magento\Migration\Code\Processor\TokenHelper';
        $this->tokenHelper = $this->getMock($className, [], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\Migration\Code\Processor\Table\TableFunctionMatcher',
            [
                'objectManager' => $this->classCbjectManager,
                'logger' => $this->logger,
                'tokenHelper' => $this->tokenHelper,
            ]
        );
    }

    /**
     * test Match
     * @param mixed[] $tokens
     * @param string $index
     * @dataProvider testMatchProvider
     */
    public function testMatch($tokens, $index)
    {
        $className = '\Magento\Migration\Code\Processor\CallArgumentCollection';
        $callArgCollection = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Migration\Code\Processor\TokenArgument';
        $tokenArgument = $this->getMock($className, [], [], '', false);

        $tokenArgument->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(T_CONSTANT_ENCAPSED_STRING);

        $tokenArgument->expects($this->once())
            ->method('getName')
            ->willReturn('catalogrule/rule_product');

        $className = '\Magento\Migration\Code\Processor\TokenArgumentCollection';
        $tokenArgCollection = $this->getMock($className, [], [], '', false);

        $tokenArgCollection->expects($this->once())
            ->method('getFirstToken')
            ->willReturn($tokenArgument);

        $callArgCollection->expects($this->atLeastOnce())
            ->method('getCount')
            ->willReturn(1);

        $callArgCollection->expects($this->once())
            ->method('getFirstArgument')
            ->willReturn($tokenArgCollection);

        $this->tokenHelper->expects($this->any())
            ->method('getCallArguments')
            ->willReturn($callArgCollection);

        $className = 'Magento\Migration\Code\Processor\Table\TableFunction\Table';
        $tableFunction = $this->getMock($className, [], [], '', false);

        $this->classCbjectManager->expects($this->once())
            ->method('create')
            ->willReturn($tableFunction);

        $return = $this->model->match($tokens, $index);
        $this->assertSame($tableFunction, $return);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testMatchProvider()
    {
        return [
            [
                'tokens' => [
                    0 =>
                        [
                            0 => 379,
                            1 => ' ',
                            2 => 101,
                            3 => 'T_WHITESPACE',
                        ],
                    1 =>
                        [
                            0 => 364,
                            1 => '=>',
                            2 => 101,
                            3 => 'T_DOUBLE_ARROW',
                        ],
                    2 =>
                        [
                            0 => 379,
                            1 => ' ',
                            2 => 101,
                            3 => 'T_WHITESPACE',
                        ],
                    3 =>
                        [
                            0 => 312,
                            1 => '$this',
                            2 => 101,
                            3 => 'T_VARIABLE',
                        ],
                    4 =>
                        [
                            0 => 363,
                            1 => '->',
                            2 => 101,
                            3 => 'T_OBJECT_OPERATOR',
                        ],
                    5 =>
                        [
                            0 => 310,
                            1 => 'getTable',
                            2 => 101,
                            3 => 'T_STRING',
                        ],
                    6 => '(',
                    7 =>
                        [
                            0 => 318,
                            1 => '\'catalogrule/rule_product\'',
                            2 => 101,
                            3 => 'T_CONSTANT_ENCAPSED_STRING',
                        ],
                    8 => ')',
                    9 => ')',
                    10 => ')',
                ],
                'index' => '3',
            ]
        ];
    }
}
