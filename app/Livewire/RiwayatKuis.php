<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RiwayatKuis extends Component
{
    use WithPagination;

    public $statusFilter = 'all'; // all, selesai, sedang_mengerjakan

    public function mount()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'selesai' => 'green',
            'sedang_mengerjakan' => 'yellow',
            'waktu_habis' => 'red',
            default => 'gray'
        };
    }

    public function getStatusText($status)
    {
        return match($status) {
            'selesai' => 'Selesai',
            'sedang_mengerjakan' => 'Sedang Mengerjakan',
            'waktu_habis' => 'Waktu Habis',
            default => 'Unknown'
        };
    }

    public function render()
    {
        $query = HasilKuis::with(['kuis'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $riwayatKuis = $query->paginate(10);

        return view('livewire.riwayat-kuis', [
            'riwayatKuis' => $riwayatKuis
        ]);
    }
}