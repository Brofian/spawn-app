<?php

namespace SpawnCore\System\Custom\Gadgets;

use Detection\MobileDetect;

class MobileDetectAdapter {

    protected MobileDetect $mobileDetect;

    public function __construct()
    {
        $this->mobileDetect = new MobileDetect();
    }

    public function getMobileDetect(): MobileDetect {
        return $this->mobileDetect;
    }

    public function isMobile(): bool {
        return $this->mobileDetect->isMobile();
    }

    public function isTablet(): bool {
        return $this->mobileDetect->isTablet();
    }

    public function isBot(): bool {
        return $this->mobileDetect->isBot() || $this->mobileDetect->isMobileBot();
    }



}