<template>

    <section class="   h-full ">


        <swiper class="w-full h-full "
                :modules="modules"
                :autoplay="{delay:delay}"
                :loop="true"
                :speed="mode=='edit'?0:1000"
                :slides-per-view="1"
                :space-between="16"
                :pagination="{ clickable: true }"
                :scrollbar="{ draggable: true }"
                @swiper=""
                @slideChange=""
        >

            <swiper-slide v-if="mode=='edit'" v-for="(item,idx) in data" :key="`k${idx}`" class="flex flex-col      ">
                <div @click="edit(idx,'delete')"
                     class="text-white mx-auto w-fit m-1 p-2 px-4 rounded-md bg-red-500 hover:bg-red-400 hover:cursor-pointer">
                    <TrashIcon class="w-4 h-4   "/>
                </div>
                <div class="flex gap-2 items-center">
                    <div
                        @click="edit(idx,'add-left')"
                        class="  p-2 text-white rounded-md bg-green-500 hover:bg-green-400 hover:cursor-pointer">
                        <PlusIcon class="w-4 h-4  "/>
                    </div>
                    <div class="grow">
                        <ImageUploader mode="create"

                                       :preload="item.image" :ref="`imageCropper${idx}`"
                                       :label="__('image_jpg')"
                                       :id="'img-'+idx"
                                       class=" block  z-[99999999] "/>
                        <div
                            class=" text-neutral-500 my-4 p-2 flex flex-col   gap-2 ">
                            <TextInput v-model="item.link" :placeholder="__('link')"/>
                            <TextInput v-model="item.title" :placeholder="__('title')"/>
                            <TextInput v-model="item.desc" :placeholder="__('description')" multiline="true"/>

                        </div>
                    </div>
                    <div @click="edit(idx,'add-right')"
                         class="    text-white p-2  rounded-md bg-green-500 hover:bg-green-400 hover:cursor-pointer">
                        <PlusIcon
                            class="w-4 h-4  "/>
                    </div>
                </div>
            </swiper-slide>
            <swiper-slide v-else v-for="(item,idx) in items" class="relative     ">

                <Image classes="h-full w-full" :src="item.image" disabled="true"></Image>

                <div class="absolute bottom-0 text-center w-full">
                    <div v-if="item.title || item.description"
                         class=" text-white my-4 p-2 bg-primary-700 bg-opacity-40">
                        <div v-if="item.title" class="">{{ item.title }}</div>
                        <div v-if="item.description" class="text-xs text-primary-100">{{ item.description }}</div>
                    </div>
                </div>
            </swiper-slide>


        </swiper>

    </section>
</template>
<script>
import Image from "@/Components/Image.vue";

import {Alert, initTE,} from "tw-elements";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y, Autoplay} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import {
    PlayIcon,
    TrashIcon,
    PlusIcon,
} from "@heroicons/vue/24/solid";
import ImageUploader from "@/Components/ImageUploader.vue";
import TextInput from "@/Components/TextInput.vue";

export default {
    data() {
        return {
            modules: [Navigation, Pagination, /*Scrollbar,*/ A11y, Autoplay],
            data: this.items
        }
    },
    props: ['delay', 'items', 'mode'],

    components: {
        TextInput,
        ImageUploader,
        Image,
        Swiper,
        SwiperSlide,
        TrashIcon,
        PlusIcon,

    },
    mounted() {


    },
    methods: {
        edit(idx, cmnd) {
            if (cmnd == 'add-right')
                this.data.splice(idx + 1, 0, {title: null, desc: null, image: null, link: null})
            else if (cmnd == 'add-left')
                this.data.splice(idx, 0, {title: null, desc: null, image: null, link: null})
            else if (cmnd == 'delete')
                this.data.splice(idx, 1)

            if (this.data.length == 0)
                this.data.splice(0, 0, {title: null, desc: null, image: null, link: null})


        },
        getItems() {
            for (let i in this.items) {
                this.items[i].image = this.$refs[`imageCropper${i}`][0].getCroppedData()
            }
            return this.items
        }
    }
}
</script>
