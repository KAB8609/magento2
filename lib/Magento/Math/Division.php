<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Math;

/**
 * Division library
 */
class Division
{
    /**
     * Const for correct dividing decimal values
     */
    const DIVIDE_EPSILON = 10000;

    /**
     * Returns the floating point remainder (modulo) of the division of the arguments
     *
     * @param float|int $dividend
     * @param float|int $divisor
     * @return float|int
     */
    public function getExactDivision($dividend, $divisor)
    {
        $epsilon = $divisor / self::DIVIDE_EPSILON;

        $remainder = fmod($dividend, $divisor);
        if (abs($remainder - $divisor) < $epsilon || abs($remainder) < $epsilon) {
            $remainder = 0;
        }

        return $remainder;
    }
}
