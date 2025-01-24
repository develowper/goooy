<template>
    <Scaffold>
        <div class="  container py-4 max-w-sm mx-auto">


            <div
                class="m-1 p-4  bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">

                <div class="flex flex-col items-stretch pb-10">
                    <div class="flex flex-col items-center">
                        <ApplicationLogo class="h-24 rounded-full shadow-lg"/>
                        <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ __('app_name') }}</h5>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('shop') }}</span>
                    </div>
                    <div class="flex flex-col section_bg_color rounded-lg p-2 py-4    space-y-2">
                        <small class="section_header_text_color  ">{{ __('redirect_after_select') }}</small>
                        <PrimaryButton :ref="idx" v-for="(price,idx) in $page.props.prices" classes="text-sm"
                                       @click=" $refs[idx][0].setLoading(true)  ; tgOpenLink(price.key).then(()=>{
                                           $refs[idx][0].setLoading(false)  ;
                                       })"
                                       :label="`${price.name} | ${asPrice(price.price)} ${__('currency')}`">
                        </PrimaryButton>

                    </div>
                </div>
                <!--                <div>{{ tgGetInitData() }}</div>-->
                <!--                <div class="text-primary">{{ tgUser }}</div>-->
            </div>
        </div>
    </Scaffold>

</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import WebApp from '@twa-dev/sdk'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Scaffold from "@/Layouts/Scaffold.vue";

export default {
    name: "Index.vue",
    data() {
        return {
            loading: false,
            platform: null,


        }
    },
    props: ['navbarTheme'],
    components: {
        Head,
        Link,
        ApplicationLogo,
        Scaffold,
        PrimaryButton,
    },
    mounted() {
        WebApp.ready();

        // WebApp.expand();

    },
    methods: {}
}
</script>

<style scoped>

</style>
