<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <a href="{{ route('admin.news.index') }}" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                    Back to news
                </a>
            </div>

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
                                    class="site-textarea mt-2 block w-full px-4 py-3 focus:outline-none focus:ring-0"
                                >{{ old('excerpt', $announcement->excerpt) }}</textarea>
                                <p class="copy-faint mt-2 text-xs">Optional. Leave blank to auto-generate it from the body.</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-4">
                                    <x-label value="Body" />
                                    <p class="copy-faint text-xs">Browser editor. No Markdown required.</p>
                                </div>

                                <div class="editor-shell" data-rich-editor>
                                    <div class="editor-toolbar" data-tiptap-toolbar>
                                        {{-- Inline formatting --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool" data-tt="bold" title="Bold (⌘B)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="italic" title="Italic (⌘I)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="underline" title="Underline (⌘U)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"/><line x1="4" y1="21" x2="20" y2="21"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="strike" title="Strikethrough">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" y1="12" x2="20" y2="12"/></svg>
                                            </button>
                                        </div>

                                        {{-- Headings --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool editor-tool--label" data-tt="h1" title="Heading 1">H1</button>
                                            <button type="button" class="editor-tool editor-tool--label" data-tt="h2" title="Heading 2">H2</button>
                                            <button type="button" class="editor-tool editor-tool--label" data-tt="h3" title="Heading 3">H3</button>
                                        </div>

                                        {{-- Lists & quote --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool" data-tt="bulletList" title="Bullet list">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="4" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1" fill="currentColor" stroke="none"/><line x1="8" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="8" y1="18" x2="20" y2="18"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="orderedList" title="Numbered list">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="blockquote" title="Quote">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                                            </button>
                                            <button type="button" class="editor-tool" data-tt="codeBlock" title="Code block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                            </button>
                                        </div>

                                        {{-- Link (icon replaced by JS based on cursor state) --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool" data-tt="link" data-tt-link-state="link" title="Insert link (⌘K)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                            </button>
                                        </div>

                                        {{-- Alignment cycle (icon replaced by JS) --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool" data-tt="align" data-tt-align="left" title="Text alignment">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="14" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                                            </button>
                                        </div>

                                        {{-- Text color --}}
                                        <div class="editor-group">
                                            <label class="editor-tool editor-color-label" title="Text color">
                                                <span class="editor-color-a">A</span>
                                                <span class="editor-color-dot" data-tt-color-dot style="background:#1f251d"></span>
                                                <input type="color" class="sr-only" data-tt="color" value="#1f251d">
                                            </label>
                                        </div>

                                        {{-- Clear formatting --}}
                                        <div class="editor-group">
                                            <button type="button" class="editor-tool" data-tt="clear" title="Clear formatting">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/><line x1="2" y1="22" x2="22" y2="2"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Link input bar --}}
                                    <div class="editor-link-bar" data-tt-link-bar hidden>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5;flex-shrink:0"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                        <input type="url" class="editor-link-input" data-tt-link-input placeholder="Paste or type a link…">
                                        <button type="button" class="editor-link-btn editor-link-btn--apply" data-tt-link-apply>Apply</button>
                                        <button type="button" class="editor-link-btn editor-link-btn--cancel" data-tt-link-cancel>✕</button>
                                    </div>

                                    <div data-rich-editor-content></div>

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
