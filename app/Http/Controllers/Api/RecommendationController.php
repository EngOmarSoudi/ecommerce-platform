<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        private RecommendationService $recommendationService
    ) {}

    /**
     * GET /api/recommendations?user_id=...&type=...
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'type' => 'required|in:personalized,trending,recently_viewed',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $userId = $request->input('user_id');
        $type = $request->input('type');
        $limit = $request->input('limit', 10);

        $recommendations = match($type) {
            'personalized' => $this->recommendationService->getPersonalizedRecommendations($userId, $limit),
            'trending' => $this->recommendationService->getTrendingProducts($limit),
            'recently_viewed' => $this->recommendationService->getRecentlyViewed($limit),
            default => [],
        };

        return response()->json([
            'type' => $type,
            'count' => count($recommendations),
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * GET /api/recommendations/also-bought/{product}
     */
    public function alsoBought(int $productId, Request $request): JsonResponse
    {
        $limit = $request->input('limit', 6);

        $recommendations = $this->recommendationService->getAlsoBought($productId, $limit);

        return response()->json([
            'product_id' => $productId,
            'type' => 'also_bought',
            'count' => count($recommendations),
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * GET /api/recommendations/similar/{product}
     */
    public function similar(int $productId, Request $request): JsonResponse
    {
        $limit = $request->input('limit', 6);

        $recommendations = $this->recommendationService->getSimilarItems($productId, $limit);

        return response()->json([
            'product_id' => $productId,
            'type' => 'similar_items',
            'count' => count($recommendations),
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * GET /api/user/{user}/score
     */
    public function userScore(int $userId): JsonResponse
    {
        $score = $this->recommendationService->getUserBehavioralScore($userId);

        return response()->json([
            'user_id' => $userId,
            'score' => $score,
        ]);
    }
}
