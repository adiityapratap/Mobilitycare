<?php

function is_valid_au_phone($phone) {
    static $phoneUtil = null;

    if ($phoneUtil === null) {
        // Autoload libphonenumber classes
        spl_autoload_register(function ($class) {
            if (strpos($class, 'libphonenumber\\') === 0) {
                $path = DIR_SYSTEM . 'library/libphonenumber/src/' .
                        str_replace('\\', '/', substr($class, 14)) . '.php';
                if (file_exists($path)) {
                    require_once $path;
                }
            }
        });

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    }

    try {
        $number = $phoneUtil->parse($phone, 'AU');

        if (!$phoneUtil->isValidNumberForRegion($number, 'AU')) {
            return false;
        }

        $type = $phoneUtil->getNumberType($number);

        return in_array($type, [
            \libphonenumber\PhoneNumberType::MOBILE,
            \libphonenumber\PhoneNumberType::FIXED_LINE,
            \libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE,
        ], true);

    } catch (Exception $e) {
        return false;
    }
}

function format_au_phone_e164($phone) {
    try {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $number = $phoneUtil->parse($phone, 'AU');
        return $phoneUtil->format(
            $number,
            \libphonenumber\PhoneNumberFormat::E164
        );
    } catch (Exception $e) {
        return null;
    }
}
