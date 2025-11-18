<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GdprComplianceService
{
    /**
     * Export all user data in JSON format (GDPR Article 20)
     */
    public function exportUserData(User $user): string
    {
        $data = [
            'personal_information' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'orders' => $user->orders()->with(['items', 'address'])->get()->toArray(),
            'addresses' => $user->addresses()->get()->toArray(),
            'cart_items' => $user->cartItems()->with('product')->get()->toArray(),
            'reviews' => $user->reviews()->with('product')->get()->toArray(),
            'wishlist' => $user->wishlistItems()->with('product')->get()->toArray(),
        ];
        
        // Add seller data if applicable
        if ($user->seller) {
            $data['seller_information'] = [
                'business_name' => $user->seller->business_name,
                'business_email' => $user->seller->business_email,
                'business_phone' => $user->seller->business_phone,
                'tax_id' => $user->seller->tax_id,
                'bank_account' => $user->seller->bank_account,
                'status' => $user->seller->status,
                'commission_rate' => $user->seller->commission_rate,
                'total_sales' => $user->seller->total_sales,
                'products_count' => $user->seller->products()->count(),
            ];
        }
        
        $filename = 'user_data_export_' . $user->id . '_' . now()->format('Y-m-d_His') . '.json';
        $path = 'exports/' . $filename;
        
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        
        return Storage::path($path);
    }
    
    /**
     * Anonymize user data (GDPR Article 17 - Right to be forgotten)
     */
    public function anonymizeUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Anonymize personal data
            $user->update([
                'name' => 'Deleted User #' . $user->id,
                'email' => 'deleted_' . $user->id . '@anonymized.local',
                'phone' => null,
                'birth_date' => null,
                'avatar' => null,
                'password' => bcrypt(bin2hex(random_bytes(32))),
            ]);
            
            // Anonymize addresses
            $user->addresses()->update([
                'first_name' => 'Deleted',
                'last_name' => 'User',
                'phone' => null,
                'email' => null,
                'address_line_1' => 'Anonymized',
                'address_line_2' => null,
                'city' => 'Unknown',
                'state' => 'Unknown',
                'postal_code' => '00000',
            ]);
            
            // Delete profile picture if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            
            // Keep orders for legal/accounting purposes but anonymize
            $user->orders()->update([
                'email' => 'anonymized@deleted.local',
                'phone' => null,
            ]);
            
            // Delete reviews
            $user->reviews()->delete();
            
            // Clear cart
            $user->cartItems()->delete();
            
            // Clear wishlist
            $user->wishlistItems()->delete();
            
            return true;
        });
    }
    
    /**
     * Permanently delete user account and all associated data
     */
    public function deleteUserAccount(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Delete all related data
            $user->addresses()->delete();
            $user->cartItems()->delete();
            $user->wishlistItems()->delete();
            $user->reviews()->delete();
            
            // Delete seller data if applicable
            if ($user->seller) {
                $user->seller->products()->delete();
                $user->seller->delete();
            }
            
            // Delete orders older than retention period (7 years for financial records)
            $retentionDate = now()->subYears(7);
            $user->orders()->where('created_at', '<', $retentionDate)->delete();
            
            // Delete user
            $user->delete();
            
            return true;
        });
    }
    
    /**
     * Get data retention policy compliance status
     */
    public function getRetentionStatus(): array
    {
        $retentionPeriods = [
            'orders' => 7, // years
            'user_data' => 2, // years after last activity
            'logs' => 90, // days
            'sessions' => 30, // days
        ];
        
        $status = [];
        
        // Check for data exceeding retention periods
        foreach ($retentionPeriods as $type => $period) {
            $status[$type] = [
                'retention_period' => $period,
                'requires_cleanup' => $this->checkRequiresCleanup($type, $period),
            ];
        }
        
        return $status;
    }
    
    /**
     * Check if data type requires cleanup
     */
    protected function checkRequiresCleanup(string $type, int $period): bool
    {
        switch ($type) {
            case 'orders':
                $cutoffDate = now()->subYears($period);
                return DB::table('orders')->where('created_at', '<', $cutoffDate)->exists();
                
            case 'user_data':
                $cutoffDate = now()->subYears($period);
                return User::where('updated_at', '<', $cutoffDate)
                    ->whereNull('deleted_at')
                    ->exists();
                
            case 'logs':
                // Check application logs
                return false; // Implement based on logging strategy
                
            case 'sessions':
                $cutoffDate = now()->subDays($period);
                return DB::table('sessions')->where('last_activity', '<', $cutoffDate->timestamp)->exists();
                
            default:
                return false;
        }
    }
}
