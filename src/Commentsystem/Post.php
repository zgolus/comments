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
    /** @var  string */
    public $name;

    /** @var  string */
    public $email;

    /** @var  string */
    public $post;

    /** @var Silex\Application */
    private $container;

    public function save()
    {
        try {
            $this->container['db']->executeQuery(
                'INSERT INTO posts (name, email, post) values (?, ?, ?)',
                [
                    $this->container->escape($this->name),
                    $this->container->escape($this->email),
                    $this->container->escape($this->post)
                ]
            );
        } catch (\Exception $e) {
            $this->container['monolog']->addError('Something went wrong!');
        }
    }

    /**
     * @param Silex\Application $container
     */
    public function setContainer(Silex\Application $container)
    {
        $this->container = $container;
    }
} 