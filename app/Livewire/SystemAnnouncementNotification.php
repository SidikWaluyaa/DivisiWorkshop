<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SystemAnnouncement;
use App\Models\UserAnnouncementRead;
use Illuminate\Support\Facades\Auth;

class SystemAnnouncementNotification extends Component
{
    public $showModal = false;
    public $showToast = false;
    public $selectedAnnouncement = null;

    protected $listeners = ['refreshAnnouncements' => '$refresh'];

    public function mount()
    {
        $user = Auth::user();
        if (!$user) return;

        // Check if there is an unread active announcement
        $latestUnread = SystemAnnouncement::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->whereDoesntHave('reads', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('published_at')
            ->first();

        if ($latestUnread) {
            $this->showToast = true;
            $this->selectedAnnouncement = $latestUnread->toArray();
        }
    }

    public function openDetail($id)
    {
        $announcement = SystemAnnouncement::find($id);
        if ($announcement) {
            $this->selectedAnnouncement = $announcement->toArray();
            $this->showModal = true;
            $this->showToast = false;
            $this->markAsRead($id);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function closeToast()
    {
        $this->showToast = false;
        if ($this->selectedAnnouncement && isset($this->selectedAnnouncement['id'])) {
            $this->markAsRead($this->selectedAnnouncement['id']);
        }
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) return;

        UserAnnouncementRead::firstOrCreate([
            'user_id' => $user->id,
            'announcement_id' => $id,
        ], [
            'read_at' => now(),
        ]);

        $this->dispatch('refreshAnnouncements');
    }

    public function render()
    {
        $user = Auth::user();
        $announcements = collect();
        $unreadCount = 0;

        if ($user) {
            // Eager load reads for current user to eliminate N+1 queries
            $announcements = SystemAnnouncement::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', now());
                })
                ->with(['reads' => function($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();

            // Count unread directly using memory mapping to avoid extra count query
            $unreadCount = $announcements->filter(function($ann) {
                return $ann->reads->isEmpty();
            })->count();
        }

        return view('livewire.system-announcement-notification', [
            'announcements' => $announcements,
            'unreadCount' => $unreadCount,
        ]);
    }
}
