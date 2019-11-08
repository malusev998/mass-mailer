<?php


namespace App\Dto;


/**
 * Class CreateUser
 * @property string $name
 * @property string $surname
 * @property string $email
 * @package App\Dto
 */
class CreateUser extends Base
{
    protected $name;
    protected $surname;
    protected $email;
}