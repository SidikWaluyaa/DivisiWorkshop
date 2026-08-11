<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = SystemAnnouncement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Auto-increment suggested version
        $latest = SystemAnnouncement::orderBy('id', 'desc')->first();
        $suggestedVersion = 'v1.0.0';
        if ($latest && !empty($latest->version)) {
            $ver = str_replace('v', '', $latest->version);
            $parts = explode('.', $ver);
            if (count($parts) === 3 && is_numeric($parts[2])) {
                $parts[2] = (int)$parts[2] + 1;
                $suggestedVersion = 'v' . implode('.', $parts);
            } else {
                $suggestedVersion = 'v' . ($ver + 0.1);
            }
        }

        // Get latest work log file if available
        $latestWorkLogData = $this->parseLatestWorkLog();

        return view('admin.announcements.index', compact('announcements', 'suggestedVersion', 'latestWorkLogData'));
    }

    public function fetchWorkLog()
    {
        $data = $this->parseLatestWorkLog();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan berkas laporan kerja harian (laporan_kerja_*.md).'
            ], 444);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function parseLatestWorkLog()
    {
        $files = glob(base_path('laporan_kerja_*.md'));
        if (empty($files)) {
            return null;
        }

        // Sort by modified time / filename descending
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $filename = basename($latestFile);
        $content = file_get_contents($latestFile);

        $title = '🚀 Pembaruan Fitur & Optimasi Sistem (' . date('d M Y') . ')';
        $summary = 'Pembaruan dan optimasi performa sistem terbaru untuk mempermudah operasional tim.';
        $description = '';

        // Extract Judul Rilis if present
        if (preg_match('/##\s*🚀?\s*Judul Rilis\s*\n+([^\n#]+)/i', $content, $mTitle)) {
            $title = trim($mTitle[1]);
        }

        // Extract Ringkasan Rilis if present
        if (preg_match('/##\s*💡?\s*Ringkasan Rilis[^\n]*\n+([^\n#]+)/i', $content, $mSummary)) {
            $summary = trim($mSummary[1]);
        }

        // Extract Detail Pembaruan bullet points
        preg_match_all('/^-\s+\*\*(.*?)\*\*:\s*(.*)$/m', $content, $matches, PREG_SET_ORDER);
        $bulletPoints = [];
        if (!empty($matches)) {
            foreach ($matches as $m) {
                $bulletPoints[] = "• " . $m[1] . ": " . $m[2];
            }
        } else {
            // Fallback line scan for bullet points
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (str_starts_with($trimmed, '-') || str_starts_with($trimmed, '*')) {
                    $bulletPoints[] = "• " . ltrim($trimmed, '-* ');
                }
            }
        }

        if (count($bulletPoints) > 0) {
            $description = "Pembaruan rilis fitur dan perbaikan sistem terbaru:\n" . implode("\n", array_slice($bulletPoints, 0, 10));
            if (empty($mSummary)) {
                $summary = str_replace('• ', '', $bulletPoints[0]);
            }
        } else {
            $description = "Rilis fitur baru dan penyempurnaan alur kerja sistem.";
        }

        if (strlen($summary) > 200) {
            $summary = substr($summary, 0, 197) . '...';
        }

        return [
            'filename' => $filename,
            'title' => $title,
            'category' => 'FEATURE_UPDATE',
            'summary' => $summary,
            'description' => $description,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'required|string|max:20',
            'category' => 'required|in:FEATURE_UPDATE,MAINTENANCE,SYSTEM_NOTICE,BUG_FIX',
            'summary' => 'nullable|string|max:500',
            'description' => 'required|string',
        ]);

        SystemAnnouncement::create([
            'version' => $request->version,
            'title' => $request->title,
            'category' => $request->category,
            'summary' => $request->summary,
            'description' => $request->description,
            'target_roles' => ['all'],
            'is_active' => true,
            'created_by' => Auth::id(),
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengumuman rilis fitur baru berhasil dipublikasikan!');
    }

    public function update(Request $request, $id)
    {
        $announcement = SystemAnnouncement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'required|string|max:20',
            'category' => 'required|in:FEATURE_UPDATE,MAINTENANCE,SYSTEM_NOTICE,BUG_FIX',
            'summary' => 'nullable|string|max:500',
            'description' => 'required|string',
        ]);

        $announcement->update([
            'version' => $request->version,
            'title' => $request->title,
            'category' => $request->category,
            'summary' => $request->summary,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Pengumuman rilis berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $announcement = SystemAnnouncement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
