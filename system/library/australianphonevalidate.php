<?php
// system/library/AustralianPhone.php

class Australianphonevalidate {

    private $phoneUtil;

    public function __construct() {

        // Register a PSR-4–like autoloader for libphonenumber
        spl_autoload_register(function ($class) {
            if (strpos($class, 'libphonenumber\\') === 0) {
                $path = DIR_SYSTEM . 'library/libphonenumber/src/' .
                        str_replace('\\', '/', substr($class, 14)) . '.php';

                if (file_exists($path)) {
                    require_once $path;
                }
            }
        });

        $this->phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    }

    /**
     * Validate AU mobile or landline
     */
    public function isValid($phone) {
        try {
            $number = $this->phoneUtil->parse($phone, 'AU');

            if (!$this->phoneUtil->isValidNumberForRegion($number, 'AU')) {
                return false;
            }

            $type = $this->phoneUtil->getNumberType($number);

            return in_array($type, array(
                \libphonenumber\PhoneNumberType::MOBILE,
                \libphonenumber\PhoneNumberType::FIXED_LINE,
                \libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE
            ));

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Normalize to E.164 format
     */
    public function formatE164($phone) {
        try {
            $number = $this->phoneUtil->parse($phone, 'AU');
            return $this->phoneUtil->format(
                $number,
                \libphonenumber\PhoneNumberFormat::E164
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
