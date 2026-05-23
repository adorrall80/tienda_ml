<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'cancelado' => 'Cancelado',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
    ];

    public const ESTADOS_PAGO = [
        'pendiente' => 'Pendiente',
        'pagado' => 'Pagado',
        'rechazado' => 'Rechazado',
    ];

    protected $fillable = [
        'numero',
        'user_id',
        'cliente_nombre',
        'cliente_email',
        'cliente_telefono',
        'direccion',
        'comuna',
        'ciudad',
        'notas',
        'subtotal',
        'envio',
        'total',
        'estado',
        'estado_pago',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function internalNotes()
    {
        return $this->hasMany(OrderInternalNote::class);
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst((string) $this->estado);
    }

    public function nextActionLabel(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Contactar a la tienda para coordinar la solicitud.',
            'confirmado' => 'Coordinar entrega o retiro directamente con la tienda.',
            'enviado' => 'Esperar la confirmacion de entrega.',
            'entregado' => 'Solicitud entregada. Guarda este detalle como respaldo.',
            'cancelado' => 'Solicitud cancelada. Si corresponde, contacta a la tienda.',
            default => 'Revisar el estado de la solicitud.',
        };
    }

    public function recordStatusChange(User $user, string $newStatus, string $actor): bool
    {
        if ($this->estado === $newStatus) {
            return false;
        }

        $oldStatus = $this->estado;

        $this->update(['estado' => $newStatus]);

        $this->statusHistories()->create([
            'user_id' => $user->id,
            'actor' => $actor,
            'estado_anterior' => $oldStatus,
            'estado_nuevo' => $newStatus,
        ]);

        return true;
    }
}
