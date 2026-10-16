<?php

namespace Database\Factories;

use App\Models\Popup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Popup>
 */
class PopupFactory extends Factory
{
    protected $model = Popup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'config' => [
                'type' => 'image',
                'title' => $this->faker->sentence(4),
                'message' => $this->faker->paragraph(2),
                'image' => null,
                'image_position' => 'top',
                'show_title' => true,
                'show_message' => true,
                'button_text' => 'Shop Now',
                'button_url' => '/shop',
                'button_color' => '#4f46e5',
                'text_color' => '#111827',
                'bg_color' => '#ffffff',
                'overlay_opacity' => 60,
                'rounded' => '2xl',
                'trigger' => 'after_seconds',
                'trigger_seconds' => 3,
                'scroll_percent' => 50,
                'frequency' => 'once_per_session',
                'hide_after_close' => true,
                'hide_after_click' => false,
                'show_on' => ['all'],
                'selected_pages' => [],
                'devices' => ['all'],
                'customers' => ['everyone'],
                'start' => '',
                'end' => '',
            ],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
