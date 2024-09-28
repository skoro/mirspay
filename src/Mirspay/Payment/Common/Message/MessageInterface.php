<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Message;

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    /**
     * @return mixed The raw message representation.
     */
    public function getRawData(): mixed;
}
