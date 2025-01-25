<template>

    <Panel>
        <template v-slot:header>
            <title>{{__('panel')}}</title>
        </template>


        <template v-slot:content>
            <!-- Content header -->
            <div
                class="flex items-center justify-between px-4 py-2 text-primary-500 border-b md:py-4">
                <div class="flex">
                    <Bars2Icon class="h-7 w-7 mx-3"/>
                    <h1 class="text-2xl font-semibold">{{ __('skin') }}</h1>
                </div>
                <div>
                    <button v-if="false" @click="params.id=null;params.key=null;params.value=null;modal.show()"
                            data-te-toggle="modal"
                            data-te-target="#settingModal"
                            data-te-ripple-init
                            class="inline-flex items-center  justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold  transition-all duration-500 text-white     hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        {{ __('new_setting') }}
                    </button>
                </div>
            </div>
            <!-- Content -->
            <div class="px-2 flex flex-col   md:px-4">

                <div class="flex-col p-2  bg-white  overflow-x-auto shadow-lg  rounded-lg">

                    <div class="flex flex-col p-1 md:p-3 border rounded-lg  ">
                        <div class="p-1">{{ __('slider') }}</div>

                        <div v-if="$page.props.slider.value.length>0"
                             class=" p-3 z-[1100]  shadow-md bg-white rounded-lg     w-full         ">
                            <Slider ref="slider" :items="$page.props.slider.value" mode="edit"></Slider>
                        </div>
                        <PrimaryButton
                            @click="showDialog('danger',__('sure_to_edit?'),__('accept'),edit,{ 'id':$page.props.slider.id,'key':$page.props.slider.key, 'value':    $refs.slider.getItems() || []   }) "
                            type="button" class="  m-2 flex justify-center"
                            :class="{ 'opacity-25': loading }"
                            :disabled="loading">
                            <LoadingIcon class="w-4 h-4 mx-3 " v-if="loading"/>
                            <span v-else class=" text-sm  ">  {{ __('register_info') }}</span>
                        </PrimaryButton>
                    </div>

                </div>

            </div>

            <!-- Modal -->
            <div
                data-te-modal-init
                class="fixed left-0 top-0 backdrop-blur z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
                id="settingModal"
                tabindex="-1"
                aria-labelledby="settingModalLabel"
                aria-hidden="true">
                <div
                    data-te-modal-dialog-ref
                    class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 px-2 sm:px-4 md:px8 min-[576px]:max-w-5xl">
                    <div
                        class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none">
                        <div
                            class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4">
                            <!--Modal title-->
                            <h5
                                class="text-xl font-medium leading-normal text-neutral-800"
                                id="settingModalLabel">

                            </h5>
                            <!--Close button-->
                            <button
                                :class="`text-danger`"
                                type="button"
                                class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                                data-te-modal-dismiss
                                aria-label="Close">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-6 w-6">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!--Modal body-->
                        <div class="relative flex-auto p-4" data-te-modal-body-ref>
                            <div
                                class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
                                <FolderPlusIcon class="h-7 w-7 mx-3"/>

                                <h1 class="text-2xl font-semibold">{{ __('new_setting') }}</h1>

                            </div>


                            <div class="px-2  md:px-4">

                                <div
                                    class="    mx-auto md:max-w-3xl   mt-6 px-2 md:px-4 py-4   overflow-hidden  rounded-lg  ">


                                    <div
                                        class="flex flex-col mx-2   col-span-2 w-full     px-2"
                                    >

                                        <form @submit.prevent="addEditData">


                                            <div class="my-2">
                                                <TextInput
                                                    id="key"
                                                    type="text"
                                                    :placeholder="__('key')"
                                                    classes="  "
                                                    v-model="params.key"
                                                    autocomplete="key"
                                                    :error="params.errors.key"
                                                >
                                                    <template v-slot:prepend>
                                                        <div class="p-3">
                                                            <Bars2Icon class="h-5 w-5"/>
                                                        </div>
                                                    </template>

                                                </TextInput>
                                            </div>

                                            <div class="my-2">
                                                <TextInput
                                                    id="value"
                                                    type="text"
                                                    :placeholder="__('value')"
                                                    classes="  "
                                                    v-model="params.value"
                                                    autocomplete="value"
                                                    :error="params.errors.value"
                                                >
                                                    <template v-slot:prepend>
                                                        <div class="p-3">
                                                            <Bars2Icon class="h-5 w-5"/>
                                                        </div>
                                                    </template>

                                                </TextInput>
                                            </div>

                                            <div v-if="loading"
                                                 class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                                                <div
                                                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                                                    :class="{' animate-pulse': loading}"
                                                    :style="`width: 100%`">
                                                </div>
                                            </div>

                                            <div class="    mt-4">

                                                <PrimaryButton class="w-full  "
                                                               :class="{ 'opacity-25': loading}"
                                                               :disabled="loading">
                                                    <LoadingIcon class="w-4 h-4 mx-3 " v-if="  loading"/>
                                                    <span class=" text-lg  ">  {{ __('register_info') }}</span>
                                                </PrimaryButton>

                                            </div>

                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </template>


    </Panel>
</template>

<script>
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import {
    Bars2Icon,
    MagnifyingGlassIcon,
    ChevronDownIcon,
    HomeIcon,
    XMarkIcon,
    ArrowsUpDownIcon,
    FolderPlusIcon,
    PlusIcon,

} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue"
import Tooltip from "@/Components/Tooltip.vue"
import LoadingIcon from "@/Components/LoadingIcon.vue"
import {Modal} from "tw-elements";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Slider from "@/Components/Slider.vue";

export default {
    data() {
        return {
            params: {
                page: 1,
                search: null,
                paginate: this.$page.props.pageItems[0],
                order_by: null,
                dir: 'DESC',
                id: null,
                key: null,
                value: null,
                errors: {},
            },
            data: [],
            pagination: {},
            toggleSelect: false,
            loading: false,
            error: null,
            selected: null,
        }
    },
    components: {
        TextInput,
        Head,
        Link,
        HomeIcon,
        ChevronDownIcon,
        Panel,
        Bars2Icon,
        Image,
        MagnifyingGlassIcon,
        XMarkIcon,
        Pagination,
        ArrowsUpDownIcon,
        Tooltip,
        LoadingIcon,
        FolderPlusIcon,
        PlusIcon,
        PrimaryButton,
        Slider,
    },
    mounted() {

        this.getData();
        const modalEl = document.getElementById('settingModal');
        this.modal = new Modal(modalEl);
        // this.showDialog('danger', 'message',()=>{});
        // this.isLoading(false);
    },
    methods: {
        getData() {

            this.loading = true;
            this.data = [];
            window.axios.get(route('admin.panel.setting.search'), {
                params: this.params
            }, {})
                .then((response) => {
                    this.data = response.data.data;
                    this.data.forEach(el => {
                        el.selected = false;
                    });
                    delete response.data.data;
                    this.pagination = response.data;

                })

                .catch((error) => {
                    if (error.response) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        console.log(error.response.data);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                        this.error = error.response.data;

                    } else if (error.request) {
                        // The request was made but no response was received
                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                        // http.ClientRequest in node.js
                        console.log(error.request);
                        this.error = error.request;
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        console.log('Error', error.message);
                        this.error = error.message;
                    }
                    console.log(error.config);
                    this.showToast('danger', error)
                })
                .finally(() => {
                    // always executed
                    this.loading = false;
                });
        },
        toggleAll() {

            this.toggleSelect = !this.toggleSelect;
            this.data.forEach(e => {
                e.selected = this.toggleSelect;
            });
        },
        edit(params) {
            this.loading = true;
            window.axios.patch(route('admin.panel.setting.update'), params,
                {})
                .then((response) => {
                    if (response.data && response.data.message) {
                        this.modal.hide();
                        this.showToast('success', response.data.message);
                        window.location.reload();

                    }


                })

                .catch((error) => {
                    this.error = this.getErrors(error);
                    if (error.response && error.response.data) {


                    }
                    this.showToast('danger', this.error);
                })
                .finally(() => {
                    // always executed
                    this.loading = false;
                });
        },
        removeData(id) {
            this.loading = true;
            window.axios.delete(route('admin.panel.setting.delete', id), {},
                {})
                .then((response) => {
                    if (response.data && response.data.message) {
                        this.showToast('success', response.data.message);
                        this.params.page = 1;
                        this.getData();

                    }


                })

                .catch((error) => {
                    this.error = this.getErrors(error);
                    if (error.response && error.response.data) {


                    }
                    this.showToast('danger', this.error);
                })
                .finally(() => {
                    // always executed
                    this.loading = false;
                });
        },
        paginationChanged(data) {

            this.params.page = data.page;
            this.getData();
        },
        bulkAction(cmnd) {
            if (this.data.filter(e => e.selected).length == 0) {
                this.showToast('danger', this.__('nothing_selected'));
                return;
            }
            this.isLoading(true);
            const params = {
                cmnd: cmnd, data: this.data.reduce((result, el) => {
                    if (el.selected) result.push(el.id);
                    return result;
                }, [])
            };

            window.axios.patch(route('article.update'), params,
                {
                    onUploadProgress: function (axiosProgressEvent) {
                    },

                    onDownloadProgress: function (axiosProgressEvent) {
                    }
                })
                .then((response) => {
                    if (response.data && response.data.message) {
                        this.showToast('success', response.data.message);

                    }
                    if (response.data && response.data.results) {
                        const res = response.data.results;
                        for (let i in this.data)
                            for (let j in res)
                                if (res[j].id == this.data[i].id) {
                                    this.data[i].status = res[j].status;
                                    break;
                                }
                    }

                })

                .catch((error) => {
                    this.error = this.getErrors(error);

                    this.showToast('danger', this.error);
                })
                .finally(() => {
                    this.isLoading(false);
                });
        }
    },

}
</script>
