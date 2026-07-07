<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <div class="home-no-copy" @copy.prevent @cut.prevent @paste.prevent @contextmenu.prevent>
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

                <a href="#projects" class="home-projects-button" data-ui-reveal>
                    Our Software
                </a>
            </div>
        </section>

        <section
            id="projects"
            class="home-projects"
            aria-label="Our Software"
            x-data="{
                currentSlide: 0,
                slideCount: 3,
                slideTimer: null,
                init() {
                    this.startSlideTimer();
                },
                startSlideTimer() {
                    window.clearInterval(this.slideTimer);
                    this.slideTimer = window.setInterval(() => {
                        this.currentSlide = (this.currentSlide + 1) % this.slideCount;
                    }, 8000);
                },
                showSlide(index) {
                    this.currentSlide = index;
                    this.startSlideTimer();
                },
            }"
        >
            <div class="home-software-carousel" data-ui-reveal>
                <div class="home-software-carousel__viewport">
                    <div
                        class="home-software-carousel__track"
                        :style="`transform: translateX(-${currentSlide * 100}%);`"
                    >
                        <div class="home-software-carousel__slide home-software-carousel__slide--blue" role="img" aria-label="Software preview one"></div>
                        <div class="home-software-carousel__slide home-software-carousel__slide--dark" role="img" aria-label="Software preview two"></div>
                        <div class="home-software-carousel__slide home-software-carousel__slide--light" role="img" aria-label="Software preview three"></div>
                    </div>
                </div>

                <button
                    type="button"
                    class="home-software-carousel__control home-software-carousel__control--previous"
                    aria-label="Previous software preview"
                    @click="showSlide((currentSlide + slideCount - 1) % slideCount)"
                ></button>

                <button
                    type="button"
                    class="home-software-carousel__control home-software-carousel__control--next"
                    aria-label="Next software preview"
                    @click="showSlide((currentSlide + 1) % slideCount)"
                ></button>

                <div class="home-software-carousel__dots" aria-label="Software preview selector">
                    <template x-for="slideIndex in slideCount" :key="slideIndex">
                        <button
                            type="button"
                            class="home-software-carousel__dot"
                            :key="`${slideIndex}-${currentSlide === slideIndex - 1}`"
                            :class="{ 'home-software-carousel__dot--active': currentSlide === slideIndex - 1 }"
                            :aria-label="`Show software preview ${slideIndex}`"
                            @click="showSlide(slideIndex - 1)"
                        ></button>
                    </template>
                </div>
            </div>
        </section>
    </div>
</x-public-layout>
