<?php

use function Livewire\Volt\{state};

state([
    'activeTab' => 'framework',
    'copiedCommand' => null,
]);

$selectTab = function (string $tab) {
    $this->activeTab = $tab;
};

$copyCommand = function (string $key) {
    $this->copiedCommand = $key;
};

?>

<div class="border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] overflow-hidden">
    <!-- Faux-OS Window Chrome -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-[#EAEAEA] dark:border-[#262626] bg-[#FAFAFA] dark:bg-[#141413]">
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="ml-2 font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">stack-telemetry.volt</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-[#EDF3EC] text-[#346538] dark:bg-[#1E2E20] dark:text-[#78C280]">
                Livewire v4 Ready
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#172B38] dark:text-[#6CB9E8]">
                Volt Active
            </span>
        </div>
    </div>

    <!-- Interactive Workspace Body -->
    <div class="p-6">
        <!-- Sub-Navigation / Tabs -->
        <div class="flex items-center gap-1 pb-3 mb-5 border-b border-[#EAEAEA] dark:border-[#262626]">
            <button 
                type="button" 
                wire:click="selectTab('framework')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'framework' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                01. Framework
            </button>
            <button 
                type="button" 
                wire:click="selectTab('livewire')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'livewire' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                02. Livewire & Volt
            </button>
            <button 
                type="button" 
                wire:click="selectTab('terminal')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'terminal' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                03. Dev Routine
            </button>
        </div>

        @if ($activeTab === 'framework')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Application Core</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Laravel {{ app()->version() }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">PHP Runtime</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">PHP {{ PHP_VERSION }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Environment</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[#FBF3DB] text-[#956400] dark:bg-[#2F2917] dark:text-[#E0BE69]">
                        {{ app()->environment() }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Styling Engine</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Tailwind CSS v4.0</span>
                </div>
            </div>
        @elseif ($activeTab === 'livewire')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Reactive Engine</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Livewire 4.x</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Single-File Paradigm</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Livewire Volt v1.x</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Mounted Directory</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">resources/views/livewire</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Volt Status</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[#EDF3EC] text-[#346538] dark:bg-[#1E2E20] dark:text-[#78C280]">
                        Compiled & Synchronized
                    </span>
                </div>
            </div>
        @else
            <div class="space-y-2.5 font-mono text-xs">
                <div class="p-3 bg-[#FBFBFA] dark:bg-[#1C1C1B] border border-[#EAEAEA] dark:border-[#262626] rounded-[6px]">
                    <div class="flex items-center justify-between text-[#787774] dark:text-[#8E8D8A] mb-1">
                        <span>Concurrent Server</span>
                        <div class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">composer</kbd>
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">run</kbd>
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">dev</kbd>
                        </div>
                    </div>
                    <code class="text-[#111111] dark:text-[#EDEDED] select-all">composer run dev</code>
                </div>

                <div class="p-3 bg-[#FBFBFA] dark:bg-[#1C1C1B] border border-[#EAEAEA] dark:border-[#262626] rounded-[6px]">
                    <div class="flex items-center justify-between text-[#787774] dark:text-[#8E8D8A] mb-1">
                        <span>Volt Scaffolding</span>
                        <div class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">php</kbd>
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">artisan</kbd>
                            <kbd class="px-1.5 py-0.5 text-[10px] bg-[#F7F6F3] dark:bg-[#262626] border border-[#EAEAEA] dark:border-[#383838] rounded-[3px]">make:volt</kbd>
                        </div>
                    </div>
                    <code class="text-[#111111] dark:text-[#EDEDED] select-all">php artisan make:volt &lt;component-name&gt;</code>
                </div>
            </div>
        @endif
    </div>

    <!-- Micro Footer Status bar -->
    <div class="px-6 py-2.5 bg-[#FBFBFA] dark:bg-[#141413] border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
        <span>Status: Telemetry nominal</span>
        <span>Latency: &lt;1ms</span>
    </div>
</div>
