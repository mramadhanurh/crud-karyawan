<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::latest()->paginate(5);

        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'nama'      => 'required',
            'telepon'   => 'required',
            'jabatan'   => 'required',
            'alamat'    => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/karyawan', $image->hashName());

        //create post
        Karyawan::create([
            'nama'     => $request->nama,
            'telepon'  => $request->telepon,
            'jabatan'  => $request->jabatan,
            'alamat'   => $request->alamat,
            'image'    => $image->hashName(),
        ]);

        //redirect to index
        return redirect()->route('karyawan.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        //validate form
        $this->validate($request, [
            'nama'      => 'required',
            'telepon'   => 'required',
            'jabatan'   => 'required',
            'alamat'    => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/karyawan', $image->hashName());

            //delete old image
            Storage::delete('public/karyawan/'.$karyawan->image);

            //update post with new image
            $karyawan->update([
            'nama'     => $request->nama,
            'telepon'  => $request->telepon,
            'jabatan'  => $request->jabatan,
            'alamat'   => $request->alamat,
            'image'    => $image->hashName(),
            ]);

        } else {

            //update post without image
            $karyawan->update([
                'nama'     => $request->nama,
                'telepon'  => $request->telepon,
                'jabatan'  => $request->jabatan,
                'alamat'   => $request->alamat,
            ]);
        }

        //redirect to index
        return redirect()->route('karyawan.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(Karyawan $karyawan)
    {
        //delete image
        Storage::delete('public/karyawan/'. $karyawan->image);

        //delete post
        $karyawan->delete();

        //redirect to index
        return redirect()->route('karyawan.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
