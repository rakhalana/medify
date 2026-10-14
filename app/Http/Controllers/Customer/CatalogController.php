<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the public product catalog homepage with filters and sorting.
     */
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['store.city', 'category', 'primaryImage'])
            ->active();

        // 1. Keyword search (case-insensitive across name and code)
        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        // 2. Category filter
        if ($request->filled('category')) {
            $categorySlug = $request->string('category')->toString();
            $query->whereHas('category', function (Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 3. Store vendor filter
        if ($request->filled('store')) {
            $storeSlug = $request->string('store')->toString();
            $query->whereHas('store', function (Builder $q) use ($storeSlug) {
                $q->where('slug', $storeSlug);
            });
        }

        // 4. Price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // 5. Sorting
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        // Active categories and stores with active product count
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn (Builder $q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $stores = Store::query()
            ->active()
            ->with('city')
            ->withCount(['products' => fn (Builder $q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'currentCategory' => $request->string('category')->toString(),
            'currentStore' => $request->string('store')->toString(),
            'currentSort' => $request->string('sort', 'latest')->toString(),
            'currentSearch' => $request->string('q')->toString(),
            'minPrice' => $request->input('min_price'),
            'maxPrice' => $request->input('max_price'),
        ]);
    }

    /**
     * Display a specific product detail page.
     */
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404, 'Produk tidak ditemukan atau sedang tidak aktif.');

        $product->load(['store.city', 'category', 'images']);

        $relatedProducts = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['store.city', 'primaryImage'])
            ->limit(4)
            ->get();

        return view('catalog.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Endpoint for live search auto-complete suggestions.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = $request->string('q')->trim()->toString();

        if (mb_strlen($term) < 2) {
            return response()->json(['data' => []]);
        }

        $suggestions = Product::query()
            ->active()
            ->search($term)
            ->with(['store.city', 'primaryImage'])
            ->limit(5)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'formatted_price' => $p->formatted_price,
                'store_name' => $p->store->name,
                'store_city' => $p->store->city->name,
                'image_url' => $p->primaryImage ? asset($p->primaryImage->path) : null,
                'url' => route('catalog.show', $p->slug),
            ]);

        return response()->json(['data' => $suggestions]);
    }

    /**
     * Endpoint for product quick-view modal details.
     */
    public function quickView(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $product->load(['store.city', 'category', 'images']);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'formatted_price' => $product->formatted_price,
            'stock' => $product->stock,
            'is_in_stock' => $product->stock > 0,
            'category_name' => $product->category->name,
            'store_name' => $product->store->name,
            'store_city' => $product->store->city->name,
            'images' => $product->images->map(fn ($img) => asset($img->path)),
            'primary_image' => $product->primaryImage ? asset($product->primaryImage->path) : null,
            'url' => route('catalog.show', $product->slug),
        ]);
    }
}
