<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{}"
        x-on:unlayer-livewire:exported.window="
            if ($event.detail.id === @js($getId())) {
                window.Livewire.find(@js($this->getId())).$set(@js($getStatePath()), $event.detail.state, @js($shouldSyncLive()))
            }
        "
    >
    <livewire:unlayer-livewire.editor
        :state="$getState()"
        :display-mode="$getDisplayMode()"
        :height="$getHeight()"
        :sync-live="$shouldSyncLive()"
        :unlayer-options="$getAdditionalOptions()"
        :template-search="$getTemplateSearchOptions()"
        :template-picker="$getTemplatePickerOptions()"
        :editor-id="$getId()"
        wire:key="{{ $getId() }}-livewire-editor"
    />
    </div>
</x-dynamic-component>