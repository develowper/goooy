<template>
  <Scaffold navbar-theme="dark">
    <template v-slot:header>
      <title>{{page=='shipping'?__('address'):__('cart')}}</title>

    </template>
    <div
        class="  py-8  shadow-md bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-primary-400 to-primary-500">

    </div>

    <div v-if="cart" class="flex flex-col   md:flex-row p-2 lg:p-4 lg:max-w-4xl mx-auto">


      <section class="flex flex-col grow  py-4 border  rounded-lg   md:rounded-e-none   bg-neutral-50">
        <primary-button
            @click="$inertia.visit(route('dz.index'))"
            classes="" class="m-2    rounded-lg">
            <span v-if="!loading">   {{
                __('return_to_shop')
              }}</span>
        </primary-button>
        <div class="p-2">{{ `${__('pre_order_details')}` }}</div>
        <div class="p-2 text-neutral-500 text-sm">{{ `${__('pre_order_description')}` }}</div>

        <!--        address section-->
        <div v-if="page=='shipping' || page=='payment'"
             class="border-b flex flex-col shadow  rounded-lg mx-1 mt-4 p-2 lg:p-4">
          <div class="text-neutral-400 pb-2">{{ __('delivery_address') }}</div>


          <AddressSelector v-if="cart.need_address ||  page!='cart' " :editable="page!='cart'" :clearable="true"
                           class=" " ref="addressSelector"
                           @change="update({address_idx:$event})"
                           :error="cart.errors &&   cart.errors.filter((e)=>e.type=='address').length>0?cart.errors.filter((e)=>e.type=='address')[0].message :null"
                           :preload-data=" cart.address " type="cart"/>


        </div>
        <div v-if=" cart.total_items==0" class="w-full p-4  items-center flex flex-col justify-center ">
          <div> {{ __('cart_is_empty') }}</div>
          <Link class="text-primary-500 hover:text-primary-400 cursor-pointer" :href="route('dz.index')"> {{
              __('shop')
            }}
          </Link>
        </div>
        <div v-if="  cart " class="shadow-md    rounded">

          <div
              :class="{'bg-danger-100':cart.errors &&   cart.errors.filter((e)=>e.type==`item`).length>0  }"
              class="     p-2 m-2    ">
            <div :class="{'text-danger-700':cart.errors &&   cart.errors.filter((e)=>e.type==`item`).length>0  }">
              <div v-if="cart.errors && cart.total_items==0">{{
                  __('cart_is_empty')
                }}
              </div>
              <div v-else>{{ __('cart') }}</div>
            </div>
            <div v-for="(item,idx) in cart.items" :key="idx"
                 class="flex p-2  flex-col my-2"
                 :class="{'bg-danger-100':cart.errors &&   cart.errors.filter((e)=>e.type==`item-${idx}`).length>0  }">
              <div class="flex items-start" v-if="item "
              >
                <div>
                  <Image :src="item.image_url"

                         classes="w-32 h-32 object-contain rounded  mx-1 "
                  />
                </div>
                <div
                    class="   w-full flex-col p-2 space-y-2 items-start">
                  <div class="flex items-center justify-between">
                    <Link
                        :href=" route( 'dz.catalog.view',{id:idx,name:item.pn})"
                        class="cursor-pointer hover:text-primary-500">
                      {{ item.name_fa || '' }}
                    </Link>
                    <div v-if="item.pn"
                         class="text-sm text-neutral-500 mx-2 ">{{
                        item.pn
                      }}
                    </div>
                  </div>
                  <div class="text-neutral-400 text-sm">{{ item.name_en }}</div>
                  <div class="flex  items-center text-sm">
                    <!--                <ShoppingBagIcon class="w-5 h-5 text-neutral-500"/>-->
                    <div class="text-neutral-600 mx-1">{{ __('qty') }}:</div>
                    <div class="text-neutral-600 mx-1">{{
                        item.qty ? `${parseFloat(item.qty)}` : 0
                      }}
                    </div>
                  </div>

                  <div class="flex  items-center text-sm">
                    <!--                    <div class="text-neutral-600 mx-1">{{ __('price_unit') }}:</div>-->
                    <!--                    <div class="text-neutral-600 mx-1">{{ asPrice(item.cart_item.product.price) }}</div>-->

                    <div class="text-neutral-600 mx-1">{{ __('price') }}:</div>
                    <div class="text-neutral-600 mx-1">{{
                        asPrice(Math.round(item.price))
                      }}
                    </div>
                    <TomanIcon class="w-5 h-5 text-neutral-400"/>

                  </div>

                </div>

              </div>
              <div v-if="item.error_message" class="text-danger-600 font-bold text-sm">
                {{ item.error_message }}
              </div>
              <div class="flex flex-wrap items-center justify-start my-2">
                <CartItemButton :product-id="idx"
                                class="flex  min-w-[100%]   xs:min-w-[50%] sm:min-w-[36%] lg:min-w-[20%]  hover:cursor-pointer"/>
                <div class="flex">
                  <div class="mx-2 ">{{ asPrice(item.price) }}</div>
                  <div>
                    <TomanIcon class=""/>
                  </div>
                </div>
              </div>
            </div>
            <!--           shipping_method-->
          </div>
          <div class="flex border-t items-center justify-end font-bold text-sm  p-4 py-2">
            <div class="text-neutral-600 mx-1">{{ __('order_price') }}:</div>
            <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_price) }}</div>
            <TomanIcon class="w-5 h-5 text-neutral-400"/>
          </div>
        </div>
      </section>

      <aside class="min-w-[15rem] sticky bg-neutral-100 my-2 md:my-0  p-2 rounded-lg   md:rounded-s-none ">
        <div class="flex flex-col md:my-4  ">

          <div class="flex  items-center text-sm  border-b p-4 py-2">
            <div class="text-neutral-600 mx-1">{{ __('cart_total_qty') }}:</div>
            <div class="text-neutral-800 mx-1">{{ cart.total_items }}</div>
          </div>

          <div class="flex  items-center text-sm  p-4 py-2">
            <div class="text-neutral-600 mx-1">{{ __('total_shipping_price') }}:</div>
            <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_shipping_price) }}</div>
            <TomanIcon class="w-5 h-5 text-neutral-400"/>
          </div>
          <div class="flex  items-center text-sm  p-4 py-2">
            <div class="text-neutral-600 mx-1">{{ __('cart_total_price') }}:</div>
            <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_items_price) }}</div>
            <TomanIcon class="w-5 h-5 text-neutral-400"/>
          </div>

          <div class="flex  items-center justify-start font-bold text-sm  p-4 py-2">
            <div class="text-neutral-600 mx-1">{{ __('total_price') }}:</div>
            <div class="text-neutral-800 mx-1">{{ asPrice(cart.total_price) }}</div>
            <TomanIcon class="w-5 h-5 text-neutral-400"/>
          </div>


          <PrimaryButton :class="{'opacity-50 disabled':cart.length==0}"
                         @click="handleNextButtonClick"
                         classes="" class="my-2">
            <span v-if="!loading">   {{
                page == 'shipping' ? __('reg_pre_order') : page == 'payment' ? cart.payment_method == 'local' ? __('reg_order') : __('pay') : __('complete_and_add_address')
              }}</span>
            <LoadingIcon v-else class="fill-white w-8 mx-auto" ref="loader" type="line-dot"/>
          </PrimaryButton>

        </div>
      </aside>

    </div>

    <LoadingIcon v-show="loading" ref="loader" type="linear"/>
  </Scaffold>

</template>

<script>
import AddressSelector from "@/Components/AddressSelector.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";
import TomanIcon from "@/Components/TomanIcon.vue";
import Image from "@/Components/Image.vue";
import Scaffold from "@/Layouts/DZScaffold.vue";
import {Head, Link} from '@inertiajs/vue3';
import heroImage from '@/../images/hero.jpg';
import {loadScript} from "vue-plugin-load-script";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {
  EyeIcon,
  MapPinIcon,
  ShoppingBagIcon,
  CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import {
  PencilIcon,
  ArrowTrendingUpIcon,
} from "@heroicons/vue/24/solid";
import SearchInput from "@/Components/SearchInput.vue";
import LocationSelector from "@/Components/LocationSelector.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {Navigation, Pagination, Scrollbar, A11y} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import CartItemButton from "@/Components/DZ/CartItemButton.vue";
import {Dropdown, initTE, Modal} from "tw-elements";
import Timestamp from "@/Components/Timestamp.vue";

export default {
  data() {
    return {
      page: 'cart',
      cart: null,
      loading: false,
      modules: [Navigation, Pagination, Scrollbar, A11y],
    }
  },
  props: ['heroText'],
  components: {
    Timestamp,
    CartItemButton,
    SearchInput,
    SecondaryButton,
    PrimaryButton,
    Scaffold,
    Head,
    LoadingIcon,
    Image,
    EyeIcon,
    Link,
    PencilIcon,
    Swiper,
    SwiperSlide,
    LocationSelector,
    MapPinIcon,
    ArrowTrendingUpIcon,
    TomanIcon,
    ShoppingBagIcon,
    AddressSelector,
    TextInput,
    CheckCircleIcon,
  },
  // mixins: [Mixin],
  setup(props) {

  }, mounted() {
    // this.setScroll(this.$refs.loader.$el);
    if (route().current('dz.checkout.shipping'))
      this.page = 'shipping';
    else if (route().current('dz.checkout.payment'))
      this.page = 'payment';

    // this.getCart();
    this.update();
    this.emitter.on('updateCart', (cart) => {
      this.cart = cart;

    });

  },
  methods: {
    handleNextButtonClick() {
      if (this.loading) return;
      if (this.page == 'cart') {
        this.update({current: 'dz.checkout.cart', next: 'dz.checkout.shipping'});
      } else if (this.page == 'shipping') {
        this.update({current: 'dz.checkout.shipping', next: 'dz.order.create', cmnd: 'create_order'});
        // this.update({current: 'dz.checkout.shipping', next: 'dz.checkout.payment'});
      }
      // else if (this.page == 'payment') {
      //   this.update({current: 'dz.checkout.payment', next: 'dz.order.create', cmnd: 'create_order_and_pay'});
      // }

    },
    update(params = {}) {
      this.isLoading(true);
      params.payment_method = params.payment_method || (this.cart ? this.cart.payment_method : null);
      params.current = `dz.checkout.${this.page}`;
      this.loading = true;
      window.axios.patch(route('dz.cart.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message && params.length > 0) {
              this.showToast('success', response.data.message);
            }

            if (response.data.cart) {
              this.updateCart(response.data.cart);
              this.cart = response.data.cart;
            }
            if (response.data.message && response.data.status) {
              this.showToast(response.data.status, response.data.message);
            }
            if (params.next) {

              if (this.cart.errors.length > 0 && this.page != 'cart')
                this.showToast('danger', this.__('please_correct_errors'));
              else if (response.data.url)
                window.location = response.data.url;
              else
                this.$inertia.visit(route(params.next));
            }
          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {
              if (error.response.data.cart) {
                this.updateCart(error.response.data.cart);
                this.cart = error.response.data.cart;
              }
            }
            this.showToast('danger', this.error);

          })
          .finally(() => {
            // always executed
            this.loading = false;
            this.isLoading(false);
          });
    },
  }

}
</script>
<style type="text/css">.turbo-progress-bar {
  position: fixed;
  display: block;
  top: 0;
  left: 0;
  height: 3px;
  background: #32CD32;
  z-index: 9999;
  transition: width 300ms ease-out,
  opacity 150ms 150ms ease-in;
  transform: translate3d(0, 0, 0);
}
</style>
<!--swiper settings in swiper tag-->
<!--:auto-height="true"-->
<!--:slides-per-view="'auto'"-->
<!--:breakpoints="{-->
<!--0: {-->
<!--slidesPerView: 1,-->
<!--},-->
<!--350: {-->
<!--slidesPerView: 1,-->

<!--},-->
<!--540: {-->
<!--slidesPerView: 2,-->

<!--},-->
<!--768: {-->
<!--slidesPerView: 3,-->

<!--},-->
<!--1100: {-->
<!--slidesPerView: 4,-->

<!--},-->
<!--1200: {-->
<!--slidesPerView: 5,-->

<!--},-->
<!--}"-->