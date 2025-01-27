<template>

    <nav :id="id" class=" w-full   top-0 ">
        <div class="  flex flex-col   mx-auto bg-white   ">
            <div class="flex items-center justify-between px-2 lg:px-4 ">
                <!-- Website Logo -->
                <div>
                    <Link :href="route('/')" class="flex  items-center py-6 px-2">
                        <ApplicationLogo class="  h-9 fill-current text-primary-600"/>
                        <span v-if="false" class="font-semibold text-primary-500 nav-item text-lg mx-2"
                        >{{ __('app_name') }}</span>

                    </Link>
                </div>
                <div
                    class="text-primary-500 animate-pulse text-center  font-bold text-xs md:text-sm p-4   shadow-primary-400       delay-300">
                    {{ $page.props.hero_text }}
                </div>
                <div class="flex items-center z-[1043]">
                    <div class="flex flex-col items-center">
                        <UserButton/>
                    </div>
                </div>
            </div>
            <div class="sticky top-0 ">
                <div id="navbar" class=" flex bg-primary-500 justify-between px-2 lg:px-4 ">

                    <!-- Primary Navbar items -->
                    <div
                        class="hidden md:flex items-center grow  justify-start  text-xs  transition-all duration-500">
                        <div class="flex items-center">
                            <!--            <Link :href="route('/')" class="px-4 nav-item" :class="navClasses('/')">-->
                            <!--              {{ __('home') }}-->
                            <!--            </Link>-->

                            <Link :href="route('shop.index')" class="nav-item" :class="navClasses('shop')">
                                {{ __('shop') }}
                            </Link>
                            <Link :href="route('article.index')" class="nav-item" :class="navClasses('article')">
                                {{ __('articles') }}
                            </Link>
                            <button @click="scrollTo('footer') " class="nav-item "
                                    :class="navClasses('page.contact_us')">
                                {{ __('contact_us') }}
                            </button>
                            <!--            <Link :href="route('page.contact_us')" class="nav-item " :class="navClasses('page.contact_us')">-->
                            <!--              {{ __('contact_us') }}-->
                            <!--            </Link>-->
                            <!--            <Link :href="route('page.contact_us')" class="nav-item" :class="navClasses('contact_us')">-->
                            <!--              {{ __('contact_us') }}-->
                            <!--            </Link>-->
                            <!--                        <Link :href="route('exchange.index')" class="nav-item" :class="navClasses('exchange')">-->
                            <!--                            {{ __('exchange') }}-->
                            <!--                        </Link>-->
                        </div>
                        <div class="flex items-center">
                            <!--            <Link :href="route('page.prices')" class="nav-item" :class="navClasses('prices')">-->
                            <!--              {{ __('prices') }}-->
                            <!--            </Link>-->
                            <!--            <Link :href="route('page.help')" class="nav-item" :class="navClasses('help')">-->
                            <!--              {{ __('help') }}-->
                            <!--            </Link>-->
                            <!--            <Link :href="route('page.contact_us')" class="nav-item" :class="navClasses('contact_us')">-->
                            <!--              {{ __('contact_us') }}-->
                            <!--            </Link>-->


                        </div>

                    </div>
                    <!-- Secondary Navbar items -->
                    <div></div>
                    <!-- Mobile menu button -->
                    <div class="justify-end p-1 flex    items-center nav-item ">
                        <CartButton class="m-1"/>
                        <Bars3Icon id="mobile-menu-button"
                                   class="md:hidden mobile-menu-button h-9 w-9 hover:bg-primary-400 hover:cursor-pointer  border rounded-lg  "
                                   className="  "/>


                    </div>
                </div>
            </div>
        </div>
        <!-- mobile menu -->
        <div
            class="h-0   block sm:hidden    mobile-menu  transform transition-all duration-500  bg-primary-500 px-4 shadow-md  ">
            <div class="mobile-menu-content flex flex-col ">
                <Link :href="route('/')" class="px-4 mobile nav-item" :class="navClasses('/')">
                    {{ __('home') }}
                </Link>

                <Link :href="route('shop.index')" class="mobile nav-item" :class="navClasses('shop')">
                    {{ __('shop') }}
                </Link>
                <Link :href="route('article.index')" class="mobile nav-item " :class="navClasses('article')">
                    {{ __('articles') }}
                </Link>
                <button @click="scrollTo('footer') " class=" mobile nav-item " :class="navClasses('page.contact_us')">
                    {{ __('contact_us') }}
                </button>
            </div>
            <!--      <Link :href="route('page.contact_us')" class="nav-ite " :class="navClasses('page.contact_us')">-->
            <!--        {{ __('contact_us') }}-->
            <!--      </Link>-->
        </div>
        <!--        <hr class="border-b border-gray-100 opacity-25 my-0 py-0"/>-->
    </nav>

</template>
<script>

import LanguageButton from "@/Components/LanguageButton.vue";
import CartButton from "@/Components/CartButton.vue";
import UserButton from "@/Components/UserButton.vue";
import {Head, Link} from '@inertiajs/vue3';
import {Bars3Icon, UserIcon} from "@heroicons/vue/24/outline";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

export default {
    components: {
        ApplicationLogo,
        LanguageButton,
        UserButton,
        Bars3Icon,
        UserIcon,
        Link,
        Head,
        CartButton,

    },
    props: ['theme', 'id'],
    data() {
        return {}
    }, mounted() {
        const btn = document.querySelector("svg.mobile-menu-button");
        const menu = document.querySelector("div.mobile-menu");
        const menuContent = document.querySelector("div.mobile-menu-content");

        function slide() {

            if (menu.style.height === "0px" || menu.style.height === "") {
                // Measure the height of the content
                const contentHeight = menuContent.offsetHeight;
                // Set the height dynamically
                menu.style.height = `${contentHeight}px`;
                menuContent.classList.add('opacity-0')

            } else {
                // Collapse the menu
                menu.style.height = "0px";
                menuContent.classList.remove('opacity-0')


            }
        }

        menuContent.classList.add('opacity-0')

        btn.addEventListener("click", () => {
            slide()
            menuContent.classList.toggle('opacity-0')
        });

        // Add a transition end event listener to clean up
        // menu.addEventListener("transitionend", () => {
        //     if (menu.style.height !== "0px") {
        //         // Reset height to auto after animation for responsiveness
        //         menu.style.height = "auto";
        //     }
        // });


        this.setScrollListener();


    },
    methods: {
        navClasses(item) {
            let base = " py-4 rounded-lg px-2 lg:px-2    font-semibold  transition    hover:bg-primary-400 hover:text-white  duration-300 ";
            if (item && (this.route().current(`${item}.*`) || this.route().current(`${item}`)))
                base = "py-4 active rounded-lg px-2 lg:px-2 text-primary-500  bg-primary-100   font-semibold  transition    hover:bg-primary-400 hover:text-white  duration-300 ";
            return base;
        },
        setScrollListener() {
            var scrollpos = window.scrollY;
            var nav = document.getElementsByTagName("nav")[0];
            var links = document.querySelectorAll(".nav-item");
            var buttons = [];// document.querySelectorAll(".btn");
            if (this.theme == 'light') {
                nav.classList.remove("bg-transparent");
                nav.classList.add("bg-white");
                nav.classList.remove("text-white");
                nav.classList.add("text-primary-500");
                nav.classList.add("shadow-lg");

                for (let el of links) {
                    el.classList.remove("text-white");
                    el.classList.add("text-primary-500");
                }
                for (let el of buttons) {
                    el.classList.remove("bg-white");
                    el.classList.add("bg-primary-500");
                    el.classList.remove("text-primary-500");
                    el.classList.add("text-white");
                    el.classList.remove("border-primary-500");
                    el.classList.add("border-white");
                }
                return;
            } else {

                nav.classList.add("bg-transparent");
                nav.classList.remove("bg-white");
                nav.classList.add("text-white");
                nav.classList.remove("text-primary-500");
                nav.classList.remove("shadow-lg");

                for (let el of links) {
                    if (!el.classList.contains("active")) {
                        el.classList.add("text-white");
                        el.classList.remove("text-primary-500");
                    }
                }
                for (let el of buttons) {
                    el.classList.remove("bg-white");
                    el.classList.add("bg-primary-500");
                    el.classList.remove("text-primary-500");
                    el.classList.add("text-white");
                    el.classList.remove("border-primary-500");
                    el.classList.add("border-white");
                }

            }
            return;
            document.addEventListener("scroll", function () {
                /*Apply classes for slide in bar*/
                scrollpos = window.scrollY;
                for (let el of links) {
                    if (el.classList.contains('mobile')) continue;
                    el.classList.remove("text-primary-500");
                    el.classList.add("text-white");
                }
                if (scrollpos > 10) {
                    nav.classList.remove("bg-transparent");
                    nav.classList.add("bg-white");
                    nav.classList.remove("text-white");
                    nav.classList.add("text-primary-500");
                    nav.classList.add("shadow-lg");

                    for (let el of links) {
                        if (el.classList.contains('mobile')) continue;
                        el.classList.remove("text-white");
                        el.classList.add("text-primary-500");

                    }
                    for (let el of buttons) {
                        el.classList.add("bg-white");
                        el.classList.remove("bg-primary-500");
                        el.classList.add("text-primary-500");
                        el.classList.remove("text-white");
                        el.classList.add("border-primary-500");
                        el.classList.remove("border-white");
                    }
                } else {
                    nav.classList.add("bg-transparent");
                    nav.classList.remove("bg-white");
                    nav.classList.add("text-white");
                    nav.classList.remove("text-primary-500");
                    nav.classList.remove("shadow-lg");

                    for (let el of links) {

                        if (!el.classList.contains("active")) {
                            el.classList.add("text-white");
                            el.classList.remove("text-primary-500");
                        } else {
                            el.classList.add("text-primary-500");
                            el.classList.remove("text-white");
                        }
                    }
                    for (let el of buttons) {
                        el.classList.remove("bg-white");
                        el.classList.add("bg-primary-500");
                        el.classList.remove("text-primary-500");
                        el.classList.add("text-white");
                        el.classList.remove("border-primary-500");
                        el.classList.add("border-white");
                    }

                }
            });

        }
    }
}
</script>

<style lang="scss">
.nav-item {
    //color: white;
}
</style>
