<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    //Get API for getting balances
    public function getBalances(Request $request)
    {
        $page = $request->query('page', 1);

        // Set the number of items per page to 10
        $perPage = 10;

        // Paginate assets based on the page number and fixed perPage
        $assets = Asset::paginate($perPage);

        // Return the paginated data along with pagination details
        return response()->json([
            'data' => $assets->items(),  // Paginated data
            'pagination' => [
                'total' => $assets->total(),                   // Total number of items
                'per_page' => $assets->perPage(),               // Number of items per page
                'current_page' => $assets->currentPage(),       // Current page number
                'last_page' => $assets->lastPage(),             // Last page number
                'next_page_url' => $assets->nextPageUrl(),     // URL for next page (if any)
                'prev_page_url' => $assets->previousPageUrl(), // URL for previous page (if any)
            ]
        ]);
    }

    public function deleteAsset($id)
    {
        // Attempt to find the asset
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json([
                'message' => 'Asset not found',
            ], 404);
        }

        // Delete the asset
        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted successfully',
        ], 200);
    }

   public function updatePurchaseBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        // Find the asset by ID
        $asset = Asset::findOrFail($id);

        // Get the last transaction for this asset
        $lastTransaction = Transaction::where('asset_id', $asset->id)->orderBy('created_at', 'desc')->first();

        // Initialize the opening balance
        $closing_balance = $asset->closing_balance; // Default to the asset's opening balance
        $opening_balance = $asset->opening_balance; // Default to the asset's closing balance

        if (!$lastTransaction || $lastTransaction->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            $opening_balance = $closing_balance;
            $asset->purchases = 0;
        }

        // Create a new transaction record
        $transaction = new Transaction([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'purchases',
            'quantity' => $validated['quantity'],
            'opening_balance' => $asset->opening_balance,
            'closing_balance' => $asset->closing_balance + $validated['quantity'],
        ]);
        $transaction->save();

        // Update the asset balances
        $asset->closing_balance +=  $validated['quantity'];
        $asset->purchases += $validated['quantity'];
        $asset->net_movements += $validated['quantity'];
        $asset->save();

        return response()->json([
            'message' => 'Purchase balance updated successfully.',
            'transaction' => $transaction,
            'asset' => $asset,
        ]);
    }

    // API for updating the balance for a transfer in transaction
    public function updateTransferInBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $asset = Asset::findOrFail($id);
        // Get the last transaction for this asset
        $lastTransaction = Transaction::where('asset_id', $asset->id)->orderBy('created_at', 'desc')->first();

        $closing_balance = $asset->closing_balance; // Default to the asset's opening balance
        $opening_balance = $asset->opening_balance; // Default to the asset's closing balance

        if (!$lastTransaction || $lastTransaction->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            $opening_balance = $closing_balance;
            $asset->transfers_in = 0;
        }

        $transaction = new Transaction([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'transfer_in',    //use transfer_in instead of transfers_in as it is expected
            'quantity' => $validated['quantity'],
            'opening_balance' => $asset->opening_balance,
            'closing_balance' => $asset->closing_balance + $validated['quantity'],
        ]);
        $transaction->save();

        $asset->closing_balance += $validated['quantity'];
        $asset->transfers_in += $validated['quantity'];
        $asset->net_movements += $validated['quantity'];
        $asset->save();

        return response()->json([
            'message' => 'Transfer in balance updated successfully.',
            'transaction' => $transaction,
            'asset' => $asset,
        ]);
    }

    // API for updating the balance for a transfer out transaction
    public function updateTransferOutBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $asset = Asset::findOrFail($id);

        $lastTransaction = Transaction::where('asset_id', $asset->id)->orderBy('created_at', 'desc')->first();

        // Check if the last transaction is on the same date as today
        $currentDate = now()->format('Y-m-d');  // Get today's date in 'Y-m-d' format
        $lastTransactionDate = $lastTransaction ? $lastTransaction->created_at->format('Y-m-d') : null;

        $closing_balance = $asset->closing_balance; // Default to the asset's opening balance
        $opening_balance = $asset->opening_balance; // Default to the asset's closing balance
        if ($lastTransactionDate !== $currentDate) {
            $opening_balance = $closing_balance; // New opening balance
            $closing_balance = 0;
            $asset->transfers_out = 0;
        }

        $transaction = new Transaction([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'transfer_out',   //use transfer_out instead of transfers_out
            'quantity' => $validated['quantity'],
            'opening_balance' => $asset->opening_balance,
            'closing_balance' => $asset->closing_balance - $validated['quantity'],
        ]);
        $transaction->save();

        // Update asset balances
        $asset->closing_balance -= $validated['quantity'];
        $asset->transfers_out += $validated['quantity'];
        $asset->net_movements -= $validated['quantity'];
        $asset->save();

        return response()->json([
            'message' => 'Transfer out balance updated successfully.',
            'transaction' => $transaction,
            'asset' => $asset,
        ]);
    }


    public function transferAsset(Request $request)
    {
        // Check if the authenticated user is a logistics officer
        // if (Auth::user()->role !== 'logistics_officer') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|numeric|min:1',
            'source_base_id' => 'required|exists:bases,id',
            'target_base_id' => 'required|exists:bases,id|different:source_base_id',
        ]);

        $asset = Asset::where('id', $validatedData['asset_id'])
            ->where('base_id', $validatedData['source_base_id'])
            ->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found in source base'], 404);
        }

        if ($asset->closing_balance < $validatedData['quantity']) {
            return response()->json(['message' => 'Insufficient stock in source base'], 400);
        }

        // Deduct from source base
        $asset->transfers_out += $validatedData['quantity'];
        $asset->closing_balance -= $validatedData['quantity'];
        $asset->net_movements = $asset->purchases + $asset->transfers_in - $asset->transfers_out;
        $asset->save();

        // Add to target base
        $targetAsset = Asset::firstOrNew([
            'name' => $asset->name,
            'type' => $asset->type,
            'base_id' => $validatedData['target_base_id'],
        ]);

        $targetAsset->opening_balance = $targetAsset->opening_balance ?? 0;
        $targetAsset->purchases = $targetAsset->purchases ?? 0;
        $targetAsset->transfers_in += $validatedData['quantity'];
        $targetAsset->closing_balance += $validatedData['quantity'];
        $targetAsset->net_movements = $targetAsset->purchases + $targetAsset->transfers_in - $targetAsset->transfers_out;
        $targetAsset->save();

        return response()->json(['message' => 'Transfer successful', 'data' => [
            'source_asset' => $asset,
            'target_asset' => $targetAsset,
        ]]);
    }
}

