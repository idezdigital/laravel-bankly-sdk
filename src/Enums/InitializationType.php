<?php

namespace Idez\Bankly;

enum InitializationType: string
{
    case Manual = 'Manual';
    case Key = 'Key';
    case StaticQrCode = 'StaticQrCode';
    case DynamicQrCode = 'DynamicQrCode';
}
