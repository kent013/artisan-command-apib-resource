<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Japan()
 * @method static static USA()
 * @method static static Singapore()
 * @method static static Korea()
 * @method static static HongKong()
 * @method static static Taiwan()
 * @method static static Thailand()
 * @method static static Indonesia()
 * @method static static Malaysia()
 * @extends Enum<self::*>
 */
final class Region extends Enum
{
    const Japan = 'japan';
    const USA = 'usa';
    const Singapore = 'singapore';
    const Korea = 'korea';
    const HongKong = 'hongkong';
    const Taiwan = 'taiwan';
    const Thailand = 'thailand';
    const Indonesia = 'indonesia';
    const Malaysia = 'malaysia';
}
