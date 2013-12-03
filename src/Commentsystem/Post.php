<?php
/**
 * Created by PhpStorm.
 * User: Jakub.Zgolinski
 * Date: 03.12.13
 * Time: 17:02
 */

namespace Commentsystem;

use Silex;

/**
 * Class Post
 */
class Post
{
    /** @var  integer */
    public $id;

    /** @var  string */
    public $name;

    /** @var  string */
    public $email;

    /** @var  string */
    public $post;

    /** @var Silex\Application */
    private $container;

    /**
     * @return mixed
     */
    public function save()
    {
        try {
            if ($this->id) {
                return $this->update();
            } else {
                return $this->insert();
            }
        } catch (\Exception $e) {
            $this->container['monolog']->addError('Something went wrong!');
            return false;
        }
    }

    public function delete()
    {
        try {
            return $this->container['db']->delete('posts', ['id' => $this->id]);
        } catch (\Exception $e) {
            $this->container['monolog']->addError('Something went wrong!');
            return false;
        }
    }

    /**
     * @param Silex\Application $container
     */
    public function setContainer(Silex\Application $container)
    {
        $this->container = $container;
    }

    /**
     * @return integer
     */
    private function insert()
    {
        $this->container['db']->insert(
            'posts',
            [
                'name' => $this->container->escape($this->name),
                'email' => $this->container->escape($this->email),
                'post' => $this->container->escape($this->post)
            ]
        );
        return $this->id = $this->container['db']->lastInsertId();
    }

    /**
     * @return integer
     */
    private function update()
    {
        return $this->container['db']->update(
            'posts',
            [
                'name' => $this->container->escape($this->name),
                'email' => $this->container->escape($this->email),
                'post' => $this->container->escape($this->post)
            ],
            ['id' => $this->id]
        );
    }
} 