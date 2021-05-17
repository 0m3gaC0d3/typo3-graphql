<?php

/**
 * This file is part of the "wpu_graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use Throwable;

class HttpBadRequestException extends Exception implements ClientAware
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }

    public function isClientSafe()
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'request';
    }
}
