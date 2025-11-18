<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductSimilarity;
use App\Models\UserBehavior;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputeProductSimilarities extends Command
{
    protected $signature = 'recommendations:compute-similarities {--fresh : Clear existing similarities}';
    protected $description = 'Compute product similarities using collaborative filtering';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Clearing existing similarities...');
            ProductSimilarity::truncate();
        }

        $this->info('Computing product similarities...');
        
        $products = Product::where('is_active', true)->pluck('id')->toArray();
        $totalProducts = count($products);

        if ($totalProducts < 2) {
            $this->warn('Not enough products to compute similarities.');
            return 0;
        }

        $bar = $this->output->createProgressBar($totalProducts);
        $bar->start();

        foreach ($products as $productId) {
            $similarities = $this->computeSimilaritiesForProduct($productId, $products);
            
            if (!empty($similarities)) {
                ProductSimilarity::upsert(
                    $similarities,
                    ['product_id', 'similar_product_id'],
                    ['similarity_score', 'computed_at']
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Product similarities computed successfully!');

        return 0;
    }

    /**
     * Compute similarities for a single product using collaborative filtering
     */
    private function computeSimilaritiesForProduct(int $productId, array $allProducts): array
    {
        // Get users who interacted with this product
        $usersInteractedWith = UserBehavior::where('product_id', $productId)
            ->whereIn('event_type', ['purchase', 'cart_add', 'view'])
            ->distinct('user_id')
            ->pluck('user_id')
            ->toArray();

        if (empty($usersInteractedWith)) {
            return [];
        }

        // Find other products these users interacted with
        $candidateProducts = UserBehavior::whereIn('user_id', $usersInteractedWith)
            ->where('product_id', '!=', $productId)
            ->whereIn('product_id', $allProducts)
            ->select('product_id', DB::raw('COUNT(DISTINCT user_id) as common_users'))
            ->groupBy('product_id')
            ->having('common_users', '>=', 2)
            ->get();

        $similarities = [];
        $totalUsers = count($usersInteractedWith);

        foreach ($candidateProducts as $candidate) {
            // Jaccard similarity: intersection / union
            $similarityScore = $candidate->common_users / $totalUsers;

            if ($similarityScore > 0.1) { // Minimum threshold
                $similarities[] = [
                    'product_id' => $productId,
                    'similar_product_id' => $candidate->product_id,
                    'similarity_score' => round($similarityScore, 4),
                    'similarity_type' => 'collaborative',
                    'computed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Sort by score and keep top 20
        usort($similarities, fn($a, $b) => $b['similarity_score'] <=> $a['similarity_score']);
        
        return array_slice($similarities, 0, 20);
    }
}
