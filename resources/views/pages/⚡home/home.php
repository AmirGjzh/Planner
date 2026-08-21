<?php

use Livewire\Component;

new class extends Component
{
    public function mount()
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }
};
