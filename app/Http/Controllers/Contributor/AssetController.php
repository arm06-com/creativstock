<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isContributor(), 403);

        $counts = Product::query()
            ->where('user_id', $request->user()->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('contributor.assets.index', [
            'counts' => [
                Product::STATUS_UPLOADED => $counts->get(Product::STATUS_UPLOADED, 0),
                Product::STATUS_PENDING => $counts->get(Product::STATUS_PENDING, 0),
                Product::STATUS_APPROVED => $counts->get(Product::STATUS_APPROVED, 0),
                Product::STATUS_REJECTED => $counts->get(Product::STATUS_REJECTED, 0),
            ],
            'activeStatus' => in_array($request->query('status'), [
                Product::STATUS_UPLOADED,
                Product::STATUS_PENDING,
                Product::STATUS_APPROVED,
                Product::STATUS_REJECTED,
            ], true) ? $request->query('status') : Product::STATUS_UPLOADED,
        ]);
    }
}
