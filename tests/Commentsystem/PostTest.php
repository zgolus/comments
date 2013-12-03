<?php
/**
 * Created by PhpStorm.
 * User: Jakub.Zgolinski
 * Date: 03.12.13
 * Time: 18:22
 */

use Commentsystem\Post;
use Silex\Application;
use Monolog\Logger;
use Doctrine\DBAL\Connection;

class PostTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::setContainer
     */
    public function testSuccess()
    {
        /** @var Logger $monologMock */
        $monologMock = $this->getMock('Monolog\Logger', ['addError'], [], '', false);
        $monologMock
            ->expects($this->never())
            ->method('addError');

        /** @var Connection $dbMock */
        $dbMock = $this->getMock('Doctrine\DBAL\Connection', ['executeQuery'], [], '', false);

        /** @var Application $containerMock */
        $containerMock = $this->getMock('Silex\Application', ['escape'], [], '', false);
        $containerMock
            ->expects($this->exactly(3))
            ->method('escape')
            ->will($this->returnValue(''));

        $containerMock['monolog'] = $monologMock;
        $containerMock['db'] = $dbMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->save();
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::setContainer
     */
    public function testFailure()
    {
        /** @var Logger $monologMock */
        $monologMock = $this->getMock('Monolog\Logger', ['addError'], [], '', false);
        $monologMock
            ->expects($this->once())
            ->method('addError')
            ->with('Something went wrong!');

        /** @var Connection $dbMock */
        $dbMock = $this->getMock('Doctrine\DBAL\Connection', ['executeQuery'], [], '', false);
        $dbMock
            ->expects($this->once())
            ->method('executeQuery')
            ->will($this->throwException(new Exception));

        /** @var Application $containerMock */
        $containerMock = $this->getMock('Silex\Application', ['escape'], [], '', false);
        $containerMock
            ->expects($this->exactly(3))
            ->method('escape')
            ->will($this->returnValue(''));

        $containerMock['monolog'] = $monologMock;
        $containerMock['db'] = $dbMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->save();
    }
}
 