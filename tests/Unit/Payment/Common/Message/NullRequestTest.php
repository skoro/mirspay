<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payment\Common\Message;

use App\Payment\Common\Message\NullRequest;
use PHPUnit\Framework\TestCase;

final class NullRequestTest extends TestCase
{
    public function testSendThrowsNotImplementedException(): void
    {
        $request = new NullRequest();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not implemented');

        $request->send();
    }

    public function testRawDataIsNull(): void
    {
        $request = new NullRequest();

        $this->assertNull($request->getRawData());
    }

    public function testRequestSerializesToNull(): void
    {
        $request = new NullRequest();

        $this->assertNull($request->jsonSerialize());
    }
}
