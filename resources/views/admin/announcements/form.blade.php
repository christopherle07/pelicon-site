<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="section-kicker">Admin</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-[color:var(--text-strong)]">
                    {{ $mode === 'create' ? 'New announcement' : 'Edit announcement' }}
                </h1>
            </div>

            <a href="{{ route('admin.news.index') }}" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                Back to news
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="flash-toast surface-panel p-5 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
                    {{ session('status') }}
                </div>
            @endif

            <x-validation-errors class="surface-panel p-5" />

            <form method="POST" action="{{ $mode === 'create' ? route('admin.news.store') : route('admin.news.update', $announcement) }}" class="space-y-6">
                @csrf
                @if ($mode === 'edit')
                    @method('PUT')
                @endif

                <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
                    <section class="surface-panel p-6 sm:p-8">
                        <div class="space-y-5">
                            <div>
                                <x-label for="announcement_title" value="Title" />
                                <x-input
                                    id="announcement_title"
                                    name="title"
                                    type="text"
                                    class="mt-2 block w-full"
                                    :value="old('title', $announcement->title)"
                                    maxlength="180"
                                    required
                                />
                            </div>

                            <div>
                                <x-label for="announcement_excerpt" value="Excerpt" />
                                <textarea
                                    id="announcement_excerpt"
                                    name="excerpt"
                                    rows="4"
                                    class="mt-2 block w-full px-4 py-3 focus:outline-none focus:ring-2"
                                    style="border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);"
                                >{{ old('excerpt', $announcement->excerpt) }}</textarea>
                                <p class="copy-faint mt-2 text-xs">Optional. Leave blank to auto-generate it from the body.</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-4">
                                    <x-label value="Body" />
                                    <p class="copy-faint text-xs">Browser editor. No Markdown required.</p>
                                </div>

                                <div class="editor-shell" data-rich-editor>
                                    <div class="editor-toolbar">
                                        <button type="button" class="editor-tool" data-editor-command="undo">Undo</button>
                                        <button type="button" class="editor-tool" data-editor-command="redo">Redo</button>
                                        <button type="button" class="editor-tool" data-editor-command="bold">Bold</button>
                                        <button type="button" class="editor-tool" data-editor-command="italic">Italic</button>
                                        <button type="button" class="editor-tool" data-editor-command="underline">Underline</button>
                                        <button type="button" class="editor-tool" data-editor-command="strikeThrough">Strike</button>
                                        <button type="button" class="editor-tool" data-editor-command="formatBlock" data-editor-value="p">Text</button>
                                        <button type="button" class="editor-tool" data-editor-command="formatBlock" data-editor-value="h2">H2</button>
                                        <button type="button" class="editor-tool" data-editor-command="formatBlock" data-editor-value="h3">H3</button>
                                        <button type="button" class="editor-tool" data-editor-command="insertUnorderedList">Bullets</button>
                                        <button type="button" class="editor-tool" data-editor-command="insertOrderedList">Numbers</button>
                                        <button type="button" class="editor-tool" data-editor-command="formatBlock" data-editor-value="blockquote">Quote</button>
                                        <button type="button" class="editor-tool" data-editor-command="justifyLeft">Left</button>
                                        <button type="button" class="editor-tool" data-editor-command="justifyCenter">Center</button>
                                        <button type="button" class="editor-tool" data-editor-command="justifyRight">Right</button>
                                        <button type="button" class="editor-tool" data-editor-link>Create link</button>
                                        <button type="button" class="editor-tool" data-editor-command="unlink">Unlink</button>
                                        <button type="button" class="editor-tool" data-editor-clear>Clear</button>

                                        <label class="editor-color-picker">
                                            <span class="copy-faint text-xs">Color</span>
                                            <input type="color" value="#f2f2f2" data-editor-color>
                                        </label>

                                        <label class="editor-size-picker">
                                            <span class="copy-faint text-xs">Size</span>
                                            <select data-editor-size>
                                                <option value="">Default</option>
                                                <option value="0.875rem">Small</option>
                                                <option value="1rem">Normal</option>
                                                <option value="1.25rem">Large</option>
                                                <option value="1.5rem">XL</option>
                                            </select>
                                        </label>
                                    </div>

                                    <div class="editor-canvas rich-copy" contenteditable="true" spellcheck="true" data-rich-editor-content></div>

                                    <textarea name="body" hidden data-rich-editor-input>{{ old('body', $announcement->body) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="surface-panel p-6 sm:p-8">
                        <div class="space-y-5">
                            <div>
                                <x-label for="announcement_cover_image_url" value="Cover image URL" />
                                <x-input
                                    id="announcement_cover_image_url"
                                    name="cover_image_url"
                                    type="url"
                                    class="mt-2 block w-full"
                                    :value="old('cover_image_url', $announcement->cover_image_url)"
                                    placeholder="https://..."
                                />
                            </div>

                            <div>
                                <x-label for="announcement_embed_url" value="Embed URL" />
                                <x-input
                                    id="announcement_embed_url"
                                    name="embed_url"
                                    type="url"
                                    class="mt-2 block w-full"
                                    :value="old('embed_url', $announcement->embed_url)"
                                    placeholder="YouTube or Vimeo URL"
                                />
                                <p class="copy-faint mt-2 text-xs">YouTube and Vimeo links will render as embeds on the article page.</p>
                            </div>

                            <div class="surface-panel-alt p-5">
                                <p class="section-kicker">Publishing</p>
                                <p class="copy-muted mt-3 text-sm leading-7">
                                    Save a draft while you are still writing, or publish immediately when it is ready.
                                </p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button type="submit" name="status" value="draft" class="button-secondary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                                    {{ $announcement->status === 'draft' ? 'Save draft' : 'Update as draft' }}
                                </button>

                                <button type="submit" name="status" value="published" class="button-primary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                                    {{ $announcement->status === 'published' ? 'Update published post' : 'Publish announcement' }}
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </form>

            @if ($mode === 'edit')
                <section class="surface-panel p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker">Danger Zone</p>
                            <p class="copy-muted mt-3 text-sm leading-7">
                                Delete this announcement permanently if you no longer want it available in the admin panel.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            @if ($announcement->status === 'published')
                                <a href="{{ route('news.show', $announcement) }}" class="inline-flex text-sm font-semibold text-[color:var(--accent-strong)]">
                                    View live post
                                </a>
                            @endif

                            <form method="POST" action="{{ route('admin.news.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition" style="background: var(--danger-soft); color: var(--danger);">
                                    Delete announcement
                                </button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
