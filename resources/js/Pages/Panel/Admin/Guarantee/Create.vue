<template>

  <Panel>
    <template v-slot:header>
      <title>{{__('new_product')}}</title>
    </template>


    <template v-slot:content>
      <!-- Content header -->
      <div
          class="flex items-center justify-start px-4 py-2 text-primary-500 border-b md:py-4">
        <FolderPlusIcon class="h-7 w-7 mx-3"/>

        <h1 class="text-2xl font-semibold">{{ __('new_guarantee') }}</h1>

      </div>


      <div class="px-2  md:px-4">

        <div
            class="    mx-auto md:max-w-2xl   mt-6 px-2 md:px-4 py-4 bg-white shadow-md overflow-hidden  rounded-lg  ">


          <div
              class="flex flex-col mx-2   col-span-2 w-full     px-2"
          >
            <!--            <div class="flex-col   m-2 items-center rounded-lg max-w-xs  w-full mx-auto    ">-->
            <!--              <div class="my-2">-->
            <!--                <ImageUploader ref="imageCropper" :label="__('product_image_jpg')" id="img"-->
            <!--                               height="10" class="grow " :crop-ratio="null"/>-->
            <!--                <InputError class="mt-1 " :message="form.errors.img"/>-->
            <!--              </div>-->

            <!--            </div>-->
            <form @submit.prevent="submit">


              <div v-if="$page.props.agency && $page.props.agency.level<2" class="my-2">
                <UserSelector :colsData="['fullname','phone','agency_id' ]"
                              :labelsData="['name','phone','agency_id' ]" :link="route('admin.panel.operator.search')"
                              :label="__('operator')"
                              :id="'admin'" v-model:selected="form.operator_id" :preload="null">
                  <template v-slot:selector="props">
                    <div :class="props.selectedText?'py-2':'py-2'"
                         class=" px-4 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer flex items-center ">
                      <div class="grow">
                        {{ props.selectedText ?? __('select_operator') }}
                      </div>
                      <div v-if="props.selectedText"
                           class="bg-danger rounded p-2   cursor-pointer text-white hover:bg-danger-400"
                           @click.stop="props.clear()">
                        <XMarkIcon class="w-5 h-5"/>
                      </div>
                    </div>
                  </template>
                </UserSelector>
              </div>
              <div class="my-2">
                <PhoneFields
                    v-model:phone="form.phone"
                    v-model:phone-verify="form.phone_verify"
                    :phone-error="form.errors.phone"
                    :phone-verify-error="form.errors.phone_verify"
                    type="guarantee"
                    for="users"
                    :label="__('customer_phone')"
                    :verified="null"
                    :activeButtonText="__('send_code')"
                    :disable="null"
                    :disableEdit="null"
                />

              </div>
              <div class="my-2">
                <TextInput
                    id="guarantee_code"
                    type="number"
                    :placeholder="__('guarantee_code')"
                    classes="  "
                    v-model="form.guarantee_code"
                    autocomplete="name"
                    :error="form.errors.guarantee_code"
                >
                  <template v-slot:prepend>
                    <div class="p-3">
                      <Bars2Icon class="h-5 w-5"/>
                    </div>
                  </template>
                </TextInput>
              </div>


              <div v-if="form.progress" class="shadow w-full bg-grey-light m-2   bg-gray-200 rounded-full">
                <div
                    class=" bg-primary rounded  text-xs leading-none py-[.1rem] text-center text-white duration-300 "
                    :class="{' animate-pulse': form.progress.percentage <100}"
                    :style="`width: ${form.progress.percentage }%`">
                  <span class="animate-bounce">{{ form.progress.percentage }}</span>
                </div>
              </div>

              <div class="    mt-4">

                <PrimaryButton @click="submit" type="button" class="w-full flex items-center justify-center"
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                  <LoadingIcon class="w-4 h-4 mx-3 " v-if="  form.processing"/>
                  <span class=" text-lg  ">  {{ __('register_info') }} </span>
                </PrimaryButton>

              </div>

            </form>
          </div>


        </div>
      </div>
    </template>


  </Panel>
</template>

<script>
import Scaffold from "@/Layouts/Scaffold.vue";
import Panel from "@/Layouts/Panel.vue";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {
  ChevronDownIcon,
  HomeIcon,
  UserIcon,
  EyeIcon,
  FolderPlusIcon,
  Bars2Icon,
  ChatBubbleBottomCenterTextIcon,
  Squares2X2Icon,
  SignalIcon,
  PencilIcon,
  XMarkIcon,

} from "@heroicons/vue/24/outline";
import {QuestionMarkCircleIcon,} from "@heroicons/vue/24/solid";
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioGroup from '@/Components/RadioGroup.vue'
import LoadingIcon from "@/Components/LoadingIcon.vue";
import Popover from "@/Components/Popover.vue";
import Tooltip from "@/Components/Tooltip.vue";
import TagInput from "@/Components/TagInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import Selector from "@/Components/Selector.vue";
import ProvinceCounty from "@/Components/ProvinceCounty.vue";
import PhoneFields from "@/Components/PhoneFields.vue";
import SocialFields from "@/Components/SocialFields.vue";
import TextEditor from "@/Components/TextEditor.vue";
import UserSelector from "@/Components/UserSelector.vue";
import AddressSelector from "@/Components/AddressSelector.vue";
import CitySelector from "@/Components/CitySelector.vue";
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';

export default {

  data() {
    return {
      form: useForm({

        guarantee_code: null,
        operator_id: null,
        phone: null,
        phone_verify: null,
        produced_at: null,
        guarantee_months: null,
        batch_count: null,
        name: null,
        product_id: null,
        repo_ids: null,
        weight: null,
        grade: null,
        price: null,
        pack_id: null,
        in_repo: null,
        in_shop: null,
        uploading: false,

      }),
      img: null,

    }
  },
  components: {
    AddressSelector,
    UserSelector,
    ImageUploader,
    LoadingIcon,
    Head,
    Link,
    HomeIcon,
    ChevronDownIcon,
    Panel,
    InputLabel,
    TextInput,
    InputError,
    PrimaryButton,
    RadioGroup,
    UserIcon,
    EyeIcon,
    Checkbox,
    Popover,
    Tooltip,
    FolderPlusIcon,
    Bars2Icon,
    ChatBubbleBottomCenterTextIcon,
    TagInput,
    QuestionMarkCircleIcon,
    Selector,
    Squares2X2Icon,
    ProvinceCounty,
    PhoneFields,
    SocialFields,
    SignalIcon,
    TextEditor,
    PencilIcon,
    XMarkIcon,
    CitySelector,
    datePicker: VuePersianDatetimePicker,
  },
  mounted() {
    this.initDatePicker();
    this.form.produced_at = this.toShamsi(new Date())
    // this.log(this.$page.props)

  },
  watch: {
    form(_new, _old) {


    }
  },
  methods: {

    submit() {
      // this.img = this.$refs.imageCropper.getCroppedData();
      // this.img = this.$refs.imageCropper.getCroppedData();
      this.form.clearErrors();
      // this.isLoading(true, this.form.progress ? this.form.progress.percentage : null);


      this.form
          .post(route('admin.panel.guarantee.create'), {
            preserveScroll: false,
            onSuccess: (data) => {
              if (this.$page.props.flash.status)
                this.showAlert(this.$page.props.flash.status, this.$page.props.flash.message);
            },
            onError: () => {
              this.showToast('danger', Object.values(this.form.errors).join("<br/>"));
            },
            onFinish: (data) => {
              // this.isLoading(false,);
            },
          });
    },
    initDatePicker() {
      this.$nextTick(() => {
        document.querySelectorAll('.vpd-input-group').forEach((el) => {
          el.classList.add('flex');

        });
        document.querySelectorAll('.vpd-input-group input').forEach((el) => {
          el.classList.add('rounded');
        });
        document.querySelectorAll('.vpd-input-group label').forEach((el) => {
          el.append(` ${this.__('produced_at')} `)
          // el.classList.add('w-full');
          el.classList.add('rounded-s');
        });
      });
    },
  }
  ,

}
</script>
