<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateShortUrlRequest;
use App\Services\ShortUrlService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\ShortUrlResource;

class ShortUrlController extends Controller
{
    use ApiResponse;

    protected ShortUrlService $shortUrlService;

    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }

    public function index(): JsonResponse
    {
        $shortUrls = $this->shortUrlService->getShortUrls(request()->user());

        return $this->successResponse('Short URLs fetched successfully.',ShortUrlResource::collection($shortUrls));
    }

    /**
     * Create a short URL.
     */
    public function store(CreateShortUrlRequest $request): JsonResponse
    {
        $shortUrl = $this->shortUrlService->createShortUrl($request->user(), $request->validated());

        return $this->successResponse('Short URL created successfully.',$shortUrl);
    }

    public function redirect(string $shortCode)
    {
        $originalUrl = $this->shortUrlService->getOriginalUrl($shortCode);

        return redirect()->away($originalUrl);
    }
}
