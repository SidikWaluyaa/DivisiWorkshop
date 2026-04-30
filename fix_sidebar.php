<?php
$file = 'resources/views/livewire/cs/lead-detail-manager.blade.php';
$content = file_get_contents($file);
$target = "                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>";
// Try LF too
if (strpos($content, $target) === false) {
    $target = "                                </div>\n                            </div>\n                        </div>\n                    </div>";
}
$fix = "                                </div>\n\n                                {{-- Catatan Internal --}}\n                                <div class=\"pt-6 border-t border-slate-100\">\n                                    <p class=\"text-[10px] font-black text-slate-400 uppercase mb-3\">Catatan Internal</p>\n                                    <div class=\"bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm italic text-slate-600 leading-relaxed shadow-inner\">\n                                        \"{{ \$lead->notes ?: 'Tidak ada catatan khusus untuk lead ini.' }}\"\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>";
$newContent = str_replace($target, $fix, $content);
file_put_contents($file, $newContent);
echo "Done\n";
