<?php

namespace TronPHP\UsdtTransaction;

enum Filter: string
{
    case ALL = 'all';
    case CONFIRMED = 'confirmed';
    case UNCONFIRMED = 'unconfirmed';
}
