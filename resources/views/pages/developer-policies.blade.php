<x-public-layout title="Plugin Developer Privacy Policy - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel max-w-4xl p-8 sm:p-10">
        <p class="section-kicker">Developer Policies</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Plugin Developer Privacy Policy</h1>
        <p class="copy-base mt-3 text-sm opacity-60">Last updated: April 25, 2026</p>

        <div class="copy-base mt-8 space-y-10 text-base leading-8">

            <div>
                <h2 class="title-section text-xl font-semibold">1. Overview</h2>
                <div class="mt-3 space-y-3">
                    <p>This policy applies to all plugins built for this app.</p>
                    <p>Plugins must respect user privacy, operate transparently, and only access data necessary for their intended functionality.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">2. Local-First Principle</h2>
                <div class="mt-3 space-y-3">
                    <p>This app is designed to be local-first.</p>
                    <p>Plugins must:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Only access user files and data when required for their feature</li>
                        <li>Limit access to the minimum scope necessary</li>
                    </ul>
                    <p>User content (images, boards, files) must be treated as private.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">3. Data Access &amp; Use</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins may access or process data only when it is:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Necessary for functionality</li>
                        <li>Relevant to the feature being used</li>
                    </ul>
                    <p>Plugins must not:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Access unrelated files or system data</li>
                        <li>Collect excessive or unrelated information</li>
                    </ul>
                    <p>Data should not be retained longer than needed.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">4. External Accounts &amp; Services</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins may allow users to connect third-party accounts (e.g., social media, cloud services) if:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>The user explicitly initiates the connection</li>
                        <li>The purpose of the connection is clearly explained</li>
                        <li>Authentication is handled securely (e.g., official APIs or OAuth when available)</li>
                    </ul>
                    <p>Plugins may use access tokens or session data only as needed to perform the requested action.</p>
                    <p>Plugins must not:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Collect or store user login credentials (email/password)</li>
                        <li>Perform actions without the user's knowledge</li>
                        <li>Request more access than required</li>
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">5. Network Requests</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins may send data over the internet when required for functionality.</p>
                    <p>All network activity must be:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Relevant to the feature</li>
                        <li>Transparent to the user</li>
                        <li>Limited to what is necessary</li>
                    </ul>
                    <p>Hidden or unrelated data transmission is not allowed.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">6. Storage</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins should avoid storing user data unless necessary.</p>
                    <p>If storage is required:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Data must be minimal</li>
                        <li>Data should remain local unless explicitly required otherwise</li>
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">7. Transparency</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins must clearly disclose:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>What data they access</li>
                        <li>What data they send externally (if any)</li>
                        <li>Any third-party services used</li>
                    </ul>
                    <p>This information should be easy for users to understand.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">8. Prohibited Behavior</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins may not:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Collect or sell user data</li>
                        <li>Track users across apps or services without disclosure</li>
                        <li>Inject ads, trackers, or hidden scripts</li>
                        <li>Exfiltrate local files or metadata unrelated to functionality</li>
                        <li>Interfere with the app, system, or other plugins</li>
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">9. Security</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugins must not:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Execute malicious code</li>
                        <li>Attempt to bypass system restrictions</li>
                        <li>Compromise user data or system integrity</li>
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">10. Responsibility</h2>
                <div class="mt-3 space-y-3">
                    <p>Plugin developers are solely responsible for their plugins and their behavior.</p>
                    <p>We are not responsible for any damage, data loss, or issues caused by third-party plugins.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">11. Changes</h2>
                <div class="mt-3">
                    <p>This policy may be updated over time. Continued development of plugins for this app means acceptance of the updated policy.</p>
                </div>
            </div>

            <div>
                <h2 class="title-section text-xl font-semibold">12. Contact</h2>
                <div class="mt-3">
                    <p>Questions: <a href="mailto:devs@pelican.app" class="underline underline-offset-2">devs@pelican.app</a></p>
                </div>
            </div>

        </div>
    </section>
</x-public-layout>
