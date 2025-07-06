<?php

namespace SrvKit\Auth\Cells;

use SrvKit\Auth\Config\Auth;
use CodeIgniter\View\Cells\Cell;

class Theme extends Cell
{
    public string $theme;
    public array $themes = [
        'default' => [
            "base-100" => ["#f3f3fa", null],
            "base-200" => ["#efeff9", null],
            "base-300" => ["#e4e4f7", null],
            "base-content" => ["#1d1b2e", null],
            "primary" => ["#5c6be6", "#0c0b14"],
            "secondary" => ["#7b8cf0", "#11101c"],
            "accent" => ["#8aa6f3", "#1a1e2b"],
            "neutral" => ["#4d4a7a", "#e4e4f7"],
            "info" => ["#d07fc3", "#1a1520"],
            "success" => ["#7ddc7d", "#1c2a1c"],
            "warning" => ["#f7d76b", "#2f2a14"],
            "error" => ["#d64a3f", "#2b0e0c"],
        ],
        'dark' => [
            "base-100" => ["#1a1b2b", null],
            "base-200" => ["#181929", null],
            "base-300" => ["#161627", null],
            "base-content" => ["#c9d2f7", null],
            "primary" => ["#f7a94a", "#2b1f0c"],
            "secondary" => ["#f78c7b", "#2a0e0c"],
            "accent" => ["#d68cf7", "#2a0e2b"],
            "neutral" => ["#202132", "#b3b9e4"],
            "info" => ["#7fd9f7", "#1c2a2f"],
            "success" => ["#7ff7b3", "#1c2f26"],
            "warning" => ["#f7e77f", "#2f2c1c"],
            "error" => ["#f78c7f", "#2f1c1a"],
        ]
    ];
    // protected $view = 'style';
    public function mount(): void
    {
        $auth = config('Auth');
        $this->theme = $auth->theme;
        $this->setView('style');
    }
}
