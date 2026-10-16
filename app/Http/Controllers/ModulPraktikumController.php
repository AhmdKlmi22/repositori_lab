<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ModulPraktikum; // Sesuaikan dengan nama model di folder app/Models/
use App\Models\MataKuliah;
use Illuminate\Support\Facades\Auth;

class ModulPraktikumController extends Controller
{
    public function index()
    {
        $modul = ModulPraktikum::with('mataKuliah')->latest()->paginate(15);
        $mataKuliah = MataKuliah::all();
        $role = Auth::user()->role->nama_role ?? '';
        
        return view('modul.index', compact('modul', 'mataKuliah', 'role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mk_id' => 'required|exists:mata_kuliah,id',
            'judul_modul' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx,zip|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('modul_praktikum', $fileName, 'public');

        ModulPraktikum::create([
            'mk_id' => $request->mk_id,
            'judul_modul' => $request->judul_modul,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Modul praktikum berhasil diunggah.');
    }

    public function destroy($id)
    {
        $modul = ModulPraktikum::findOrFail($id);
        
        if ($modul->file_path && Storage::disk('public')->exists($modul->file_path)) {
            Storage::disk('public')->delete($modul->file_path);
        }
        $modul->delete();

        return back()->with('success', 'Modul praktikum berhasil dihapus.');
    }
}