<template>
  <DZScaffold navbar-theme="light">
    <div class="  container mt-20 py-2 max-w-sm mx-auto">


      <div
          class="m-1 p-2 pt-4  bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">

        <div class="flex flex-col items-stretch pb-10">
          <div class="flex flex-col items-center">
            <ApplicationLogo class="h-12  slide-in-top  "/>
            <h5 class="mb-1 text-xl slide-in-top font-medium font-bold text-gray-900 dark:text-white">
              {{ __('dz_app_name') }}</h5>
            <span class="text-sm   slide-in-top font-bold text-gray-500 dark:text-gray-400">{{
                __('dz_app_description', {'item': $page.props.catalog_count})
              }}</span>
          </div>

          <SearchInput class="mx-auto  max-w-xs my-2 " v-model="params.search" @search="getData(0)"/>

          <!--          catalogs list-->
          <section v-if="products.length>0" class="container-lg p-2  ssm:mx-auto  ">

            <div
                class="  mt-6   gap-y-3 gap-x-2 grid   sm:grid-cols-1 lg:grid-cols-1 xl:grid-cols-1 ">
              <div class="bg-white  shadow-md rounded-lg  "
                   v-for="(p,idx) in products">
                <Link :href=" route( 'dz.catalog.view',{id:p.id,name:p.pn})" :id="p.id"
                      class="overflow-hidden flex flex-col     hover:cursor-pointer hover:scale-[101%] duration-300">
                  <div class="flex flex-row">
                    <div class="md:mx-auto ssm:h-64 ssm:w-full  h-24    w-24 shadow-md  ">
                      <!--                <Image :data-lity="route('storage.variations')+`/${p.id}/thumb.jpg`"-->
                      <!--                       classes="object-cover  h-full w-full  rounded-t-lg rounded-b   "-->
                      <!--                       :src="route('storage.variations')+`/${p.id}/thumb.jpg`"></Image> -->
                      <Image classes="object-cover  h-full w-full  rounded-t-lg rounded-b  "
                             :src="p.image_url" disabled="true">

                      </Image>
                    </div>

                    <div
                        class="p-2  grow flex flex-col items-stretch justify-start items-start items-between">

                      <div class="flex items-center justify-between">
                        <div class="text-primary-600 ms-1 text-xs  ">{{ cropText(p.name_fa, 50) }}</div>
                        <!--                <div class="text-sm text-neutral-500 mx-2 ">{{ __('grade') + ' ' + p.grade }}</div>-->

                      </div>
                      <hr class="border-gray-200  m-2">
                      <div class="text-neutral-500 text-sm">{{ p.name_en }}</div>
                      <div class="text-sm">{{ __('pn') + ` : ${p.pn}` }}</div>
                      <div class="flex items-center text-sm">
                        <div v-if=" p.in_shop>0">{{ __('in_stock') + ` : ${parseFloat(p.in_shop)}` }}</div>
                        <div class="text-sm text-neutral-500 mx-2" v-if="getPack(p.pack_id)">{{
                            ` ${getPack(p.pack_id)} `
                          }}
                        </div>

                      </div>

                      <div class="flex items-center justify-end">
                        <div class="flex items-center "
                        >
                          {{ asPrice(Math.round(p.price)) }}

                        </div>
                        <TomanIcon class="w-4 h-4 mx-2"/>

                      </div>

                    </div>

                  </div>


                  <div class="flex  p-2 min-w-[100%]    ">
                    <CartItemButton :key="p.id" class="w-full " :product-id="p.id"/>
                  </div>
                </Link>

              </div>
            </div>
          </section>
          <section v-else-if="!loading   "
                   class="font-bold text-rose-500  mt-8 justify-center  flex flex-col items-center   ">
            <div>
              {{ __('no_results') }}
            </div>
          </section>
          <div ref="loader">
            <LoadingIcon v-show="loading" type="linear"/>
          </div>

          <div v-if="false" class="flex flex-col section_bg_color rounded-lg p-2 py-4 mt-4  space-y-2">
            <small class="section_header_text_color  ">{{ __('links_need_vpn') }}</small>
            <SecondaryButton :ref="idx" v-for="(link,idx) in $page.props.tutorials"
                             @click="tgOpenLink(link)"
                             :label="idx">
            </SecondaryButton>
          </div>
          <div class="border my-2"></div>
          <div v-if="false" class="flex flex-col section_bg_color rounded-lg p-2 py-4    space-y-2">
            <small class="section_header_text_color  ">{{ __('links_need_sub') }}</small>
            <PrimaryButton :ref="idx" v-for="(link,idx) in $page.props.links"
                           @click=" $refs[idx][0].setLoading(true)  ; tgOpenLink(link).then(()=>{
                                           $refs[idx][0].setLoading(false)  ;
                                       })"
                           :label="idx">
            </PrimaryButton>

          </div>
        </div>
        <!--                <div>{{ tgGetInitData() }}</div>-->
        <!--                <div class="text-primary">{{ tgUser }}</div>-->
      </div>
    </div>
  </DZScaffold>

</template>

<script>
import {Head, Link} from "@inertiajs/vue3";
import WebApp from '@twa-dev/sdk'
import ApplicationLogo from "@/Components/DZ/ApplicationLogo.vue";
import DZScaffold from "@/Layouts/DZScaffold.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import CartItemButton from "@/Components/DZ/CartItemButton.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import Image from "@/Components/Image.vue";
import {ArrowTrendingUpIcon,} from "@heroicons/vue/24/outline";

export default {
  name: "Index.vue",
  data() {
    return {
      loading: false,
      platform: null,
      params: {search: null, page: 1},
      products: [],
      total: 0,
    }
  },
  props: ['navbarTheme'],
  components: {
    TomanIcon,
    CartItemButton,
    PrimaryButton,
    SecondaryButton,
    Head,
    Link,
    ApplicationLogo,
    DZScaffold,
    SearchInput,
    LoadingIcon,
    ArrowTrendingUpIcon,
    Image,
  },
  mounted() {
    // WebApp.expand();
    this.setScroll(this.$refs.loader);
    this.getData();
  },
  methods: {
    getData(page) {

      if (page == 0) {
        this.params.page = 1;
        this.products = [];
      }

      if (this.total > 0 && this.total <= this.products.length) return;
      this.loading = true;

      window.axios.get(route('catalog.search'), {
        params: this.params
      })
          .then((response) => {
            // console.log(response)
            // this.data = this.data.concat(response.data.data);
            this.total = response.data.total;
            this.params.page = response.data.current_page + 1;
            // this.products = response.data.data;
            this.products = this.products.concat(response.data.data);
            // console.log(response.data);
          })
          .catch((error) => {
            this.error = this.getErrors(error);

            this.showToast('danger', this.error)
          })
          .finally(() => {
            // always executed
            this.loading = false;
          });
    },
    setScroll(el) {
      window.onscroll = () => {
//                    const {top, bottom, height} = this.loader.getBoundingClientRect();

        let top_of_element = el.offsetTop;
        let bottom_of_element = el.offsetTop + el.offsetHeight;
        let bottom_of_screen = window.pageYOffset + window.innerHeight;
        let top_of_screen = window.pageYOffset;

        if ((bottom_of_screen + 300 > top_of_element) && (top_of_screen < bottom_of_element + 200) && !this.loading) {
          this.getData();
          // scrolled = true;
//                        console.log('visible')
          // the element is visible, do something
        } else {
//                        console.log('invisible')
          // the element is not visible, do something else
        }
      };
    },
  }
}
</script>

<style scoped>

</style>
