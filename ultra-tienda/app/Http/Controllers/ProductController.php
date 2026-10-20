<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Reglas comunes para la imagen subida desde el formulario.
     */
    private const array IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'categories' => Category::orderBy('orden')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => self::IMAGE_RULES,
            'disponible' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeProductImage($request->file('image'));
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('orden')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => self::IMAGE_RULES,
            'remove_image' => ['sometimes', 'boolean'],
            'disponible' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($product->image);
            $validated['image'] = $this->storeProductImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredImage($product->image);
            $validated['image'] = null;
        }

        unset($validated['remove_image']);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (OrderDetail::where('product_id', $product->id)->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'No se puede eliminar el producto porque está asociado a uno o más pedidos.');
        }

        $this->deleteStoredImage($product->image);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Guarda la imagen subida en el disco público y devuelve su ruta.
     */
    private function storeProductImage(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }

    /**
     * Elimina del disco público solo las imágenes gestionadas por este módulo.
     */
    private function deleteStoredImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'products/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
