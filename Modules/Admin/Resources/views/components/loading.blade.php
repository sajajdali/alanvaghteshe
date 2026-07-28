{{-- Global Livewire Loader --}}
<div x-data>
    <template x-teleport="body">
        <div
            wire:loading
            wire:loading.attr="data-active"   {{-- فقط وقتی لودینگ فعاله، این اتریبیوت اضافه میشه --}}
            class="lw-overlay"
            aria-live="polite"
            role="status"
        >
            <div class="lw-backdrop" aria-hidden="true"></div>
            <div class="lw-box">
                <div class="spinner2">
                    <div class="cube1" style="width:20px;height:20px;"></div>
                    <div class="cube2" style="width:20px;height:20px;"></div>
                </div>
            </div>
        </div>
    </template>
</div>
@push('styles')
    <style>
        .lw-overlay{
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;           /* پیش‌فرض: کلیک‌ناپذیر */
        }

        .lw-backdrop{
            position: absolute;
            inset: 0;
            background: rgba(15,23,42,.45);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        .lw-box{ position: relative; isolation: isolate; }

        /* فقط وقتی لودینگ فعاله: قفل اسکرول + اجازهٔ گرفتن کلیک توسط اوورلی */
        html:has(.lw-overlay[data-active]){ overflow: hidden; }
        html:has(.lw-overlay[data-active]) .lw-overlay{ pointer-events: all; }
        .lw-overlay{ /* مثل قبل */ }

        .lw-backdrop{ /* مثل قبل */ }

        .lw-box{ /* مثل قبل */ }

        /* قفل اسکرول وقتی کلاس روی <html> هست */
        html.lw-scroll-locked { overflow: hidden; }

        /* فقط وقتی قفل است، اوورلی کلیک‌ها را بگیرد */
        html.lw-scroll-locked .lw-overlay { pointer-events: all; }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            const toggleLock = (lock) => {
                document.documentElement.classList.toggle('lw-scroll-locked', !!lock);
            };

            // هنگام شروع/پایان هر درخواست Livewire
            Livewire.hook('request', ({ fail, succeed }) => {
                toggleLock(true);
                const done = () => toggleLock(false);
                succeed(done); fail(done);
            });
        });
    </script>
@endpush
