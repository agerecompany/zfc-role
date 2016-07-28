<?php
namespace Agere\Role\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Agere\User\Model\User;

/**
 * Role
 */
class Role
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $mnemo;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var string
     */
    private $remove;

    /** @var User[] */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set mnemo
     *
     * @param string $mnemo
     * @return Role
     */
    public function setMnemo($mnemo)
    {
        $this->mnemo = $mnemo;

        return $this;
    }

    /**
     * Get mnemo
     *
     * @return string
     */
    public function getMnemo()
    {
        return $this->mnemo;
    }

    /**
     * Set resource
     *
     * @param string $resource
     * @return Role
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set remove
     *
     * @param string $remove
     * @return Role
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;

        return $this;
    }

    /**
     * Get remove
     *
     * @return string
     */
    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     * @return Role
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }
}
