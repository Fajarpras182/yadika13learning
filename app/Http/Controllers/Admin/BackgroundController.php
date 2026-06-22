<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackgroundSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackgroundController extends Controller
{
    public function index()
    {
        $backgrounds = BackgroundSetting::orderBy('sort_order')->get();
        return view('admin.backgrounds.index', compact('backgrounds'));
    }

    public function create()
    {
        return view('admin.backgrounds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_type' => 'required|in:image,color',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_color' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'background_type' => $request->background_type,
                'background_color' => $request->background_color ?? '#f8f9fa',
                'is_active' => $request->has('is_active'),
                'sort_order' => BackgroundSetting::max('sort_order') + 1,
            ];

            if ($request->background_type === 'image' && $request->hasFile('background_image')) {
                $imagePath = $request->file('background_image')->store('backgrounds', 'public');
                $data['background_image'] = $imagePath;
            }

            BackgroundSetting::create($data);

            DB::commit();
            return redirect()->route('admin.backgrounds.index')->with('success', 'Background berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(BackgroundSetting $background)
    {
        return view('admin.backgrounds.show', compact('background'));
    }

    public function edit(BackgroundSetting $background)
    {
        return view('admin.backgrounds.edit', compact('background'));
    }

    public function update(Request $request, BackgroundSetting $background)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_type' => 'required|in:image,color',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_color' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'background_type' => $request->background_type,
                'background_color' => $request->background_color ?? '#f8f9fa',
                'is_active' => $request->has('is_active'),
            ];

            if ($request->background_type === 'image' && $request->hasFile('background_image')) {
                // Hapus gambar lama jika ada
                if ($background->background_image && Storage::disk('public')->exists($background->background_image)) {
                    Storage::disk('public')->delete($background->background_image);
                }

                $imagePath = $request->file('background_image')->store('backgrounds', 'public');
                $data['background_image'] = $imagePath;
            }

            $background->update($data);

            DB::commit();
            return redirect()->route('admin.backgrounds.index')->with('success', 'Background berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(BackgroundSetting $background)
    {
        DB::beginTransaction();
        try {
            // Hapus gambar jika ada
            if ($background->background_image && Storage::disk('public')->exists($background->background_image)) {
                Storage::disk('public')->delete($background->background_image);
            }

            $background->delete();

            DB::commit();
            return redirect()->route('admin.backgrounds.index')->with('success', 'Background berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleActive(BackgroundSetting $background)
    {
        // Nonaktifkan semua background lain jika ini diaktifkan
        if (!$background->is_active) {
            BackgroundSetting::where('is_active', true)->update(['is_active' => false]);
        }

        $background->update(['is_active' => !$background->is_active]);

        $message = $background->is_active ? 'Background berhasil diaktifkan.' : 'Background berhasil dinonaktifkan.';
        return back()->with('success', $message);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'backgrounds' => 'required|array',
            'backgrounds.*' => 'integer|exists:background_settings,id',
        ]);

        foreach ($request->backgrounds as $order => $id) {
            BackgroundSetting::where('id', $id)->update(['sort_order' => $order + 1]);
        }

        return response()->json(['success' => true]);
    }
}
