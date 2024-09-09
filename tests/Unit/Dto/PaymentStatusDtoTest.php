<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto;

use App\Dto\PaymentStatusDto;
use App\Payment\Common\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;

final class PaymentStatusDtoTest extends TestCase
{
    public function testCastToStringPaymentResponseValues(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getTransactionId')->willReturn('');
        $response->method('getMessage')->willReturn(null);
        $response->method('getCode')->willReturn(null);
        $response->method('getRawData')->willReturn(false);

        $dto = PaymentStatusDto::makeFromPaymentGatewayResponse('123', $response);

        $this->assertEquals('123', $dto->paymentGatewayId);
        $this->assertEquals('', $dto->message);
        $this->assertEquals('', $dto->code);
        $this->assertTrue($dto->isSuccess);
        $this->assertEquals('', $dto->transactionId);
    }

    /**
     * @dataProvider paymentStatusRawDataProvider
     */
    public function testPaymentStatusDataAlwaysCastToArray(mixed $data, $expected): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getTransactionId')->willReturn('');
        $response->method('getMessage')->willReturn(null);
        $response->method('getCode')->willReturn(null);
        $response->method('getRawData')->willReturn($data);

        $dto = PaymentStatusDto::makeFromPaymentGatewayResponse('123', $response);

        $this->assertEquals($expected, $dto->data);
    }

    public function paymentStatusRawDataProvider(): array
    {
        return [
            [false, [false]],
            ['', ['']],
            [null, []],
        ];
    }
}
