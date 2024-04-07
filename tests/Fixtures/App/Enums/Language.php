<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Japanese()
 * @method static static English()
 * @method static static Korean()
 * @method static static TraditionalChinese()
 * @method static static Thai()
 * @method static static Indonesian()
 * @method static static Malay()
 * @extends Enum<self::*>
 */
final class Language extends Enum
{
    const Japanese = 'ja';
    const English = 'en';
    const Korean = 'ko';
    const TraditionalChinese = 'zh-hant';
    const Thai = 'th';
    const Indonesian = 'id';
    const Malay = 'ms';
}
