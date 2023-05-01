<?php

namespace TronPHP\UsdtTransaction;

enum Target: string
{
    case ALL = 'all';
    case ONLY_TO = 'only_to';
    case ONLY_FROM = 'only_from';
}
