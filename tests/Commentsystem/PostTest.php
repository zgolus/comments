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
     * @covers Commentsystem\Post::insert
     */
    public function testInsertSuccess()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->never())
            ->method('addError');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('insert');
        $containerMock = $this->getContainerMock();
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->save();
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::insert
     */
    public function testInsertFail()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->once())
            ->method('addError')
            ->with('Something went wrong!');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('insert')
            ->will($this->throwException(new Exception));
        $containerMock = $this->getContainerMock();
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $this->assertFalse($post->save());
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::update
     */
    public function testUpdateSuccess()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->never())
            ->method('addError');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('update');
        $containerMock = $this->getContainerMock();
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->id = rand();
        $post->save();
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::update
     */
    public function testUpdateFail()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->once())
            ->method('addError')
            ->with('Something went wrong!');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('update')
            ->will($this->throwException(new Exception));
        $containerMock = $this->getContainerMock();
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->id = rand();
        $this->assertFalse($post->save());
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::delete
     */
    public function testDeleteSuccess()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->never())
            ->method('addError');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('delete');
        $containerMock = $this->getContainerMock(false);
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->id = rand();
        $post->delete();
    }

    /**
     * @covers Commentsystem\Post::save
     * @covers Commentsystem\Post::delete
     */
    public function testDeleteFail()
    {
        $monologMock = $this->getMonologMock();
        $monologMock
            ->expects($this->once())
            ->method('addError')
            ->with('Something went wrong!');
        $dbMock = $this->getConnectionMock();
        $dbMock
            ->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new Exception));
        $containerMock = $this->getContainerMock(false);
        $containerMock['db'] = $dbMock;
        $containerMock['monolog'] = $monologMock;

        $post = new Post();
        $post->setContainer($containerMock);
        $post->id = rand();
        $this->assertFalse($post->delete());
    }

    /**
     * @param boolean $escapeCalls
     * @return Application
     */
    private function getContainerMock($escapeCalls = true)
    {
        /** @var Application $containerMock */
        $containerMock = $this->getMock(
            'Silex\Application',
            ['escape'],
            [],
            '',
            false
        );
        if ($escapeCalls) {
            $containerMock
                ->expects($this->exactly(3))
                ->method('escape')
                ->will($this->returnValue(''));
        }
        return $containerMock;
    }

    /**
     * @return Connection
     */
    private function getConnectionMock()
    {
        /** @var Connection $dbMock */
        $dbMock = $this->getMock(
            'Doctrine\DBAL\Connection',
            ['insert', 'update', 'delete', 'lastInsertId'],
            [],
            '',
            false
        );
        return $dbMock;
    }

    /**
     * @return Logger
     */
    private function getMonologMock()
    {
        /** @var Logger $monologMock */
        $monologMock = $this->getMock(
            'Monolog\Logger',
            ['addError'],
            [],
            '',
            false
        );
        return $monologMock;
    }
}
 