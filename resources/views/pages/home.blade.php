<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <section class="home-intro" aria-label="Pelicon introduction">
        <div class="home-intro__content">
            <h1 class="home-intro-title" aria-label="This is Pelicon.">
                <span data-home-typewriter>
                    <span class="home-intro-title__prefix" data-type-part data-type-text="This is">This is</span>
                    <span class="home-intro-title__main">
                        <span class="home-intro-title__accent" data-type-part data-type-text="Pelicon">Pelicon</span><span data-type-part data-type-text=".">.</span>
                    </span>
                    <span class="home-intro-cursor" aria-hidden="true"></span>
                </span>
            </h1>

            <a href="#projects" class="home-projects-button">
                Our Software
            </a>
        </div>
    </section>

    <section id="projects" class="home-projects" aria-label="Projects">
    </section>
</x-public-layout>
