<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // TAMPILKAN SEMUA KATEGORI
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // FORM TAMBAH KATEGORI
    public function create()
    {
        return view('categories.create');
    }

    // SIMPAN KATEGORI
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Category::create($request->only('nama_kategori', 'deskripsi'));

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    // FORM EDIT KATEGORI
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    // UPDATE KATEGORI
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);

        $category->update($request->only('nama_kategori', 'deskripsi'));

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    // HAPUS KATEGORI
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
