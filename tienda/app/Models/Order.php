<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Order extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'preparado' => 'Preparado',
        'cancelado' => 'Cancelado',
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

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'estado', 'slug');
    }

    public function internalNotes()
    {
        return $this->hasMany(OrderInternalNote::class);
    }

    public function estadoLabel(): string
    {
        if ($this->relationLoaded('orderStatus') && $this->orderStatus) {
            return $this->orderStatus->nombre;
        }

        return self::labelForStatus($this->estado);
    }

    public static function statusOptions(bool $onlyActive = true): array
    {
        try {
            if (! Schema::hasTable('order_statuses')) {
                return self::ESTADOS;
            }

            $query = OrderStatus::query()->orderBy('orden')->orderBy('nombre');

            if ($onlyActive) {
                $query->activos();
            }

            $options = $query->pluck('nombre', 'slug')->all();

            return $options ?: self::ESTADOS;
        } catch (QueryException) {
            return self::ESTADOS;
        }
    }

    public static function labelForStatus(?string $status): string
    {
        if (! $status) {
            return 'Sin estado';
        }

        try {
            if (Schema::hasTable('order_statuses')) {
                $label = OrderStatus::where('slug', $status)->value('nombre');

                if ($label) {
                    return $label;
                }
            }
        } catch (QueryException) {
            //
        }

        return self::ESTADOS[$status] ?? ucfirst((string) $status);
    }

    public function nextActionLabel(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Contactar a la tienda para coordinar la solicitud.',
            'confirmado' => 'Coordinar entrega o retiro directamente con la tienda.',
            'preparado' => 'La tienda preparo la solicitud. Coordina retiro o entrega directamente.',
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
