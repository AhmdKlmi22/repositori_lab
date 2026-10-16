<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliah = MataKuliah::with(['dosen', 'asisten', 'peserta'])->latest()->paginate(10);
        return view('mata_kuliah.index', compact('mataKuliah'));
    }

    public function create()
    {
        $dosens = User::whereHas('role', function($q) { $q->where('nama_role', 'Dosen'); })->get();
        $aslabs = User::whereHas('role', function($q) { $q->where('nama_role', 'Aslab'); })->get();
        return view('mata_kuliah.create', compact('dosens', 'aslabs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|string|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'hari' => 'required|string',
            'waktu' => 'required|string',
            'dosen_id' => 'required|exists:users,id',
            'aslab_ids' => 'nullable|array',
            'aslab_ids.*' => 'exists:users,id',
        ]);

        $mk = MataKuliah::create([
            'kode_mk' => $request->kode_mk,
            'nama_mk' => $request->nama_mk,
            'hari' => $request->hari,
            'waktu' => $request->waktu,
            'dosen_id' => $request->dosen_id,
        ]);

        if ($request->has('aslab_ids')) {
            $mk->asisten()->attach($request->aslab_ids);
        }

        return redirect()->route('laboran.mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mataKuliah)
    {
        $dosens = User::whereHas('role', function($q) { $q->where('nama_role', 'Dosen'); })->get();
        $aslabs = User::whereHas('role', function($q) { $q->where('nama_role', 'Aslab'); })->get();
        return view('mata_kuliah.edit', compact('mataKuliah', 'dosens', 'aslabs'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode_mk' => 'required|string|unique:mata_kuliah,kode_mk,' . $mataKuliah->id,
            'nama_mk' => 'required|string|max:255',
            'hari' => 'required|string',
            'waktu' => 'required|string',
            'dosen_id' => 'required|exists:users,id',
            'aslab_ids' => 'nullable|array',
            'aslab_ids.*' => 'exists:users,id',
        ]);

        $mataKuliah->update([
            'kode_mk' => $request->kode_mk,
            'nama_mk' => $request->nama_mk,
            'hari' => $request->hari,
            'waktu' => $request->waktu,
            'dosen_id' => $request->dosen_id,
        ]);

        if ($request->has('aslab_ids')) {
            $mataKuliah->asisten()->sync($request->aslab_ids);
        } else {
            $mataKuliah->asisten()->detach();
        }

        return redirect()->route('laboran.mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->delete();
        return redirect()->route('laboran.mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}