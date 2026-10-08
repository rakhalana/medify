<div
    x-data="{
        messages: [],
        add(message, type = 'success', duration = 4000) {
            const id = Date.now() + Math.random();
            this.messages.push({ id, message, type });
            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
        },
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    x-init="
        @if (session('success'))
            add(@js(session('success')), 'success');
        @endif
        @if (session('status'))
            add(@js(session('status')), 'info');
        @endif
        @if (session('error'))
            add(@js(session('error')), 'danger');
        @endif
        @if (session('warning'))
            add(@js(session('warning')), 'warning');
        @endif
    "
    x-on:notify.window="add($event.detail.message, $event.detail.type || 'success', $event.detail.duration || 4000)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
>
    <template x-for="item in messages" :key="item.id">
        <div
            x-show="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-dropdown border bg-white text-slate-800 transition-all"
            :class="{
                'border-emerald-200 bg-emerald-50/90 text-emerald-900': item.type === 'success',
                'border-rose-200 bg-rose-50/90 text-rose-900': item.type === 'danger',
                'border-amber-200 bg-amber-50/90 text-amber-900': item.type === 'warning',
                'border-blue-200 bg-blue-50/90 text-blue-900': item.type === 'info',
            }"
        >
            <!-- Icon -->
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="item.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template x-if="item.type === 'danger'">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
                <template x-if="item.type === 'warning'">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="item.type === 'info'">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 text-sm font-medium" x-text="item.message"></div>

            <!-- Close Button -->
            <button
                type="button"
                @click="remove(item.id)"
                class="flex-shrink-0 text-slate-400 hover:text-slate-600 p-0.5 rounded focus:outline-none"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
