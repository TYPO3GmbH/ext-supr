<?php
declare(strict_types=1);
namespace WMDB\Supr\Exception;

use TYPO3\CMS\Core\Exception;

/**
 * Exception thrown if requested content is not available in payload
 */
class UnavailableContentInPayloadException extends Exception
{
}
