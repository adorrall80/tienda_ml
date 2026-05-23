<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityBlockedTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityBlockedTermController extends Controller
{
    public function index(): View
    {
        $terms = SecurityBlockedTerm::query()
            ->orderByDesc('active')
            ->orderBy('term')
            ->paginate(50);

        return view('admin.seguridad.palabras', compact('terms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'term' => ['required', 'string', 'max:100', 'unique:security_blocked_terms,term'],
        ]);

        SecurityBlockedTerm::create([
            'term' => mb_strtolower(trim($data['term'])),
            'active' => true,
        ]);

        return back()->with('success', 'Palabra bloqueada agregada.');
    }

    public function update(Request $request, SecurityBlockedTerm $term): RedirectResponse
    {
        $data = $request->validate([
            'term' => ['required', 'string', 'max:100', 'unique:security_blocked_terms,term,'.$term->id],
            'active' => ['nullable', 'boolean'],
        ]);

        $term->update([
            'term' => mb_strtolower(trim($data['term'])),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Palabra bloqueada actualizada.');
    }

    public function destroy(SecurityBlockedTerm $term): RedirectResponse
    {
        $term->delete();

        return back()->with('success', 'Palabra bloqueada eliminada.');
    }
}
