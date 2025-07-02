<?php

declare(strict_types=1);

/**
 * Kernel.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 * @copyright 2024 Konrad Stomski
 * @license MIT
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Class Kernel.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
