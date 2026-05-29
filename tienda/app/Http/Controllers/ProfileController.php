<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.tienda', 'orderStatus'])
            ->latest()
            ->get();

        $favoriteProducts = $request->user()
            ->favoriteProducts()
            ->publicados()
            ->with(['tags', 'tienda'])
            ->withCount('favorites')
            ->latest('favorites.created_at')
            ->get();

        return view('profile.edit', [
            'user' => $request->user()->loadMissing('tienda'),
            'orders' => $orders,
            'favoriteProducts' => $favoriteProducts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('cuenta.perfil')->with('status', 'profile-updated');
    }

    public function becomeSeller(Request $request): RedirectResponse
    {
        $user = $request->user();

        Role::firstOrCreate(['name' => 'cliente']);
        $sellerRole = Role::firstOrCreate(['name' => 'vendedor']);

        if (! $user->hasRole('cliente')) {
            $user->assignRole('cliente');
        }

        if (! $user->hasRole('vendedor')) {
            $user->assignRole($sellerRole);
        }

        return $user->tienda
            ? Redirect::route('vendedor.panel')->with('success', 'Ya puedes vender con tu tienda.')
            : Redirect::route('vendedor.tienda.create')->with('success', 'Ya eres vendedor. Crea tu tienda para publicar productos.');
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);

        $order->load('items.tienda.user', 'statusHistories.user', 'orderStatus');

        return view('profile.order-show', compact('order'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
