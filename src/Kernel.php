<?php namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Class Kernel
 * @package App
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
