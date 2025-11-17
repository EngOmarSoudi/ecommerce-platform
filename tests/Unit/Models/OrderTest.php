<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Sku;
use App\Models\Unit;
use App\Models\Shipment;
use App\Models\OrderEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_orders_with_users()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-000001',
            'status' => 'pending',
            'total_amount' => 999.99,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'order_number' => 'ORD-000001',
            'status' => 'pending',
            'total_amount' => 999.99,
        ]);

        $this->assertEquals($order->user->id, $user->id);
        $this->assertEquals($user->orders->first()->id, $order->id);
    }

    /** @test */
    public function orders_can_have_multiple_items()
    {
        $order = Order::factory()->create();
        $sku1 = Sku::factory()->create(['price' => 100.00]);
        $sku2 = Sku::factory()->create(['price' => 200.00]);
        $unit = Unit::factory()->create();

        $item1 = OrderItem::factory()->create([
            'order_id' => $order->id,
            'sku_id' => $sku1->id,
            'unit_id' => $unit->id,
            'quantity' => 1,
            'price' => 100.00,
            'total_amount' => 100.00,
        ]);

        $item2 = OrderItem::factory()->create([
            'order_id' => $order->id,
            'sku_id' => $sku2->id,
            'unit_id' => $unit->id,
            'quantity' => 2,
            'price' => 200.00,
            'total_amount' => 400.00,
        ]);

        $this->assertEquals(2, $order->items()->count());
        $this->assertTrue($order->items->contains($item1));
        $this->assertTrue($order->items->contains($item2));
        $this->assertEquals(500.00, $order->items->sum('total_amount'));
    }

    /** @test */
    public function orders_can_have_shipments()
    {
        $order = Order::factory()->create([
            'order_number' => 'ORD-000001',
        ]);

        $shipment = Shipment::factory()->create([
            'order_id' => $order->id,
            'tracking_number' => 'TRK123456789',
            'carrier' => 'FedEx',
            'status' => 'shipped',
        ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'tracking_number' => 'TRK123456789',
            'carrier' => 'FedEx',
            'status' => 'shipped',
        ]);

        $this->assertEquals($shipment->order->id, $order->id);
        $this->assertEquals($order->shipment->id, $shipment->id);
    }

    /** @test */
    public function orders_can_have_events()
    {
        $order = Order::factory()->create([
            'order_number' => 'ORD-000001',
        ]);

        $event1 = OrderEvent::factory()->create([
            'order_id' => $order->id,
            'event_type' => 'created',
            'description' => 'Order created',
        ]);

        $event2 = OrderEvent::factory()->create([
            'order_id' => $order->id,
            'event_type' => 'confirmed',
            'description' => 'Order confirmed',
        ]);

        $this->assertEquals(2, $order->events()->count());
        $this->assertTrue($order->events->contains($event1));
        $this->assertTrue($order->events->contains($event2));
    }
}