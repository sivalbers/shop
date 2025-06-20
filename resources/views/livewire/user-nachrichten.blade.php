<div>
    @if (!empty($messages))
        <div x-data="{
            open: true,
            messages: @entangle('messages'),
            messageTime: @entangle('messageTime'),
            currentIndex: 0,
            progress: 0,
            timer: null,
            isWaiting: false,
            get lastIndex() {
                return this.currentIndex >= this.messages.length - 1;
            },
            startTimer() {
                this.progress = 0;
                this.isWaiting = false;
                this.timer = setInterval(() => {
                    this.progress += 1;
                    if (this.progress >= 100) {
                        clearInterval(this.timer);
                        this.isWaiting = true;
                        $wire.markAsRead(this.messages[this.currentIndex].id); // <<< HIER
                    }
                }, this.messageTime);
            },
            nextMessage() {
                if (this.lastIndex || this.progress < 100) return;
                this.currentIndex++;
                this.startTimer();
            },
            prevMessage() {
                if (this.progress < 100 || this.currentIndex === 0) return;
                this.currentIndex--;
                this.startTimer();
            },
            canClose() {
                return this.progress >= 100;
            }
        }" x-init="startTimer()" x-show="open" x-transition.opacity.duration.500ms
            x-on:click.self="if (canClose()) open = false" x-on:keydown.escape.window="if (canClose()) open = false"
            x-on:keydown.right.window="nextMessage()" x-on:keydown.left.window="prevMessage()"
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <!-- Äußerer Container: volle Höhe -->
            <div class="flex flex-col h-full max-h-[85vh] bg-white rounded shadow-lg w-full max-w-4xl overflow-hidden">

                <!-- Kopfbereich: bleibt fest -->
                <div class="px-4 py-4 flex justify-between shrink-0">
                    <div class="text-xl font-semibold text-sky-600" x-text="messages[currentIndex]?.ueberschrift"></div>
                    <div class="flex flex-col items-center">
                        <div class="text-xs text-sky-600" x-text="messages[currentIndex]?.created_at"></div>
                        <div class="text-xs text-sky-600 leading-relaxed"
                            x-text="(currentIndex + 1) + ' / ' + messages.length"></div>
                    </div>
                </div>

                <div class="border-t border-b border-sky-600 mx-4 shrink-0"></div>

                <!-- Scrollbarer Inhalt -->
                <div class="flex-1 overflow-y-auto px-4 py-2">
                    <div class="text-base whitespace-pre-line" x-text="messages[currentIndex]?.text"></div>
                </div>

                <!-- Navigationsbereich + Fortschrittsbalken: bleibt unten sichtbar -->
                <div class="p-4 border-t bg-white shrink-0">
                    <div class="flex justify-between items-center mb-2">
                        <template x-if="currentIndex > 0">
                            <button @click="prevMessage()"

                                :class="progress < 100 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                    'bg-sky-600 text-white hover:bg-sky-700'"
                                class="px-4 py-1 rounded transition-colors"
                                :disabled="progress < 100">← Zurück</button>
                        </template>

                        <template x-if="currentIndex === 0">
                            <div></div>
                        </template>

                        <template x-if="!lastIndex">
                            <button
                                :class="progress < 100 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                    'bg-sky-600 text-white hover:bg-sky-700'"
                                class="px-4 py-1 rounded transition-colors" :disabled="progress < 100"
                                @click="nextMessage()">Weiter →</button>
                        </template>

                        <template x-if="lastIndex">
                            <button
                                :class="progress < 100 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                    'bg-sky-600 text-white hover:bg-sky-700'"
                                class="px-4 py-1 rounded transition-colors" :disabled="progress < 100"
                                @click="open = false">Fenster schließen</button>
                        </template>
                    </div>

                    <div class="w-full h-2 bg-gray-200 relative overflow-hidden rounded">
                        <div class="absolute top-0 left-0 h-full bg-sky-600 transition-all duration-100"
                            :style="{ width: progress + '%' }"></div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
