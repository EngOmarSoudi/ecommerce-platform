<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    /**
     * Get reviews for a product
     */
    public function index(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);
        
        $reviews = ProductReview::where('product_id', $productId)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return response()->json($reviews);
    }

    /**
     * Submit a product review
     */
    public function store(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product'
            ], 409);
        }

        // Check if this is a verified purchase
        $isVerifiedPurchase = false;
        if ($request->filled('order_id')) {
            $order = Order::where('id', $request->input('order_id'))
                ->where('user_id', $request->user()->id)
                ->whereHas('items', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->first();

            if ($order && in_array($order->status, ['delivered', 'completed'])) {
                $isVerifiedPurchase = true;
            }
        }

        $review = ProductReview::create([
            'product_id' => $productId,
            'user_id' => $request->user()->id,
            'order_id' => $request->input('order_id'),
            'rating' => $request->input('rating'),
            'title' => $request->input('title'),
            'comment' => $request->input('comment'),
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => false, // Requires admin approval
        ]);

        return response()->json([
            'message' => 'Review submitted successfully and pending approval',
            'data' => $review
        ], 201);
    }

    /**
     * Update a review (user can edit their own review)
     */
    public function update(Request $request, int $productId, int $reviewId)
    {
        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only(['rating', 'title', 'comment']));
        $review->is_approved = false; // Reset approval after edit
        $review->save();

        return response()->json([
            'message' => 'Review updated and pending re-approval',
            'data' => $review
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, int $productId, int $reviewId)
    {
        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
