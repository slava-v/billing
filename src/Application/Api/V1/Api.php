<?php

declare(strict_types=1);

namespace Billing\Application\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     description="Api specification",
 *     version="1.0.0",
 *     title="Billing API"
 * )
 *
 * @OA\Server(
 *     url="https://Billing.local/",
 *     variables={
 *          @OA\ServerVariable(serverVariable="locale", enum={"de"}, default="de")
 *     }
 * )
 */
final class Api
{
}
