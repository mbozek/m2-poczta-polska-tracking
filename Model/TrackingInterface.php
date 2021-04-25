<?php

namespace PocztaPolska\Tracking\Model;

interface TrackingInterface
{
    public function getTrackByPackageId(string $packageId): \stdClass;
}
